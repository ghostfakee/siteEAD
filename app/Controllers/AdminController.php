<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\ContentModel;
use App\Models\SiteImageModel;
use App\Models\UserModel;

final class AdminController
{
    private ContentModel $contentModel;
    private UserModel $userModel;
    private SiteImageModel $siteImageModel;

    public function __construct()
    {
        $this->contentModel   = new ContentModel();
        $this->userModel      = new UserModel();
        $this->siteImageModel = new SiteImageModel();
    }

    public function login(): void
    {
        if (is_logged_in()) {
            header('Location: index.php');
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            $username = trim((string) ($_POST['username'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');

            if (!admin_attempt_login($username, $password)) {
                $error = 'Usuario ou senha invalidos.';
            } else {
                header('Location: index.php');
                exit;
            }
        }

        View::render('admin.login', ['error' => $error]);
    }

    public function logout(): void
    {
        admin_logout();
        header('Location: login.php');
        exit;
    }

    public function dashboard(): void
    {
        require_login();
        $content = $this->contentModel->getAll();
        $slides = $this->sortedSlides($content);
        $activeSlides = array_values(array_filter($slides, static fn(array $slide): bool => !empty($slide['active'])));

        View::render('admin.dashboard', [
            'title' => 'Dashboard',
            'active' => 'dashboard',
            'flash' => $this->consumeFlash(),
            'content' => $content,
            'slides' => $slides,
            'activeSlides' => $activeSlides,
        ]);
    }

    public function banners(): void
    {
        require_login();
        $content = $this->contentModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            $action = (string) ($_POST['action'] ?? '');
            $id = (string) ($_POST['id'] ?? '');

            if ($action === 'toggle' && $id !== '') {
                $slide = $this->findSlide($content, $id);
                if ($slide) {
                    $slide['active'] = empty($slide['active']);
                    $this->replaceSlide($content, $id, $slide);
                }
            }

            if ($action === 'delete' && $id !== '') {
                $this->deleteSlide($content, $id);
            }

            if ($action === 'order' && $id !== '') {
                $slide = $this->findSlide($content, $id);
                if ($slide) {
                    $slide['order'] = (int) ($_POST['order'] ?? 0);
                    $this->replaceSlide($content, $id, $slide);
                }
            }

            $this->contentModel->save($content);
            $this->flash('Banners atualizados com sucesso.');
            header('Location: banners.php');
            exit;
        }

        View::render('admin.banners', [
            'title' => 'Gerenciar Banners',
            'active' => 'banners',
            'flash' => $this->consumeFlash(),
            'slides' => $this->sortedSlides($content),
        ]);
    }

    public function uploadBanner(): void
    {
        require_login();
        $flash = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            $content = $this->contentModel->getAll();
            $data = $_POST;

            try {
                $image = trim((string) ($_POST['image'] ?? ''));
                if (!empty($_FILES['image_file']['name'])) {
                    $image = $this->uploadImage($_FILES['image_file']);
                    $data['image'] = $image;
                }

                $slide = $this->normalizeSlide($data);
                if ($slide['image'] === '') {
                    throw new \RuntimeException('Imagem obrigatoria.');
                }

                $content['hero']['slides'][] = $slide;
                $this->contentModel->save($content);
                $this->flash('Banner adicionado com sucesso.');
                header('Location: banners.php');
                exit;
            } catch (\Throwable $error) {
                $flash = ['message' => $error->getMessage(), 'type' => 'error'];
            }
        }

        View::render('admin.banner_form', [
            'title' => 'Novo Banner',
            'active' => 'upload',
            'flash' => $flash,
            'slide' => null,
            'isEdit' => false,
        ]);
    }

    public function editBanner(string $id): void
    {
        require_login();
        $content = $this->contentModel->getAll();
        $slide = $this->findSlide($content, $id);

        if (!$slide) {
            $this->flash('Banner nao encontrado.', 'error');
            header('Location: banners.php');
            exit;
        }

        $flash = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            $data = $_POST;
            try {
                $image = trim((string) ($_POST['image'] ?? $slide['image']));
                if (!empty($_FILES['image_file']['name'])) {
                    $image = $this->uploadImage($_FILES['image_file']);
                }
                $data['image'] = $image;
                $updated = $this->normalizeSlide($data, $slide);
                $this->replaceSlide($content, $id, $updated);
                $this->contentModel->save($content);
                $this->flash('Banner atualizado com sucesso.');
                header('Location: banners.php');
                exit;
            } catch (\Throwable $error) {
                $flash = ['message' => $error->getMessage(), 'type' => 'error'];
                $slide = $this->normalizeSlide($data, $slide);
            }
        }

        View::render('admin.banner_form', [
            'title' => 'Editar Banner',
            'active' => 'banners',
            'flash' => $flash,
            'slide' => $slide,
            'isEdit' => true,
        ]);
    }

    public function content(): void
    {
        require_login();
        $content = $this->contentModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            $content['site']['site_name']   = trim((string) ($_POST['site_name']   ?? $content['site']['site_name']));
            $content['site']['logo_text']   = trim((string) ($_POST['logo_text']   ?? $content['site']['logo_text']));
            $content['site']['privacy_text'] = trim((string) ($_POST['privacy_text'] ?? $content['site']['privacy_text']));
            $content['site']['privacy_url']  = trim((string) ($_POST['privacy_url']  ?? $content['site']['privacy_url']));

            // Logo uploads
            foreach (['logo_nav', 'logo_footer'] as $logoKey) {
                if (!empty($_FILES[$logoKey]['name'])) {
                    try {
                        $this->siteImageModel->saveUpload($_FILES[$logoKey], $logoKey);
                    } catch (\Throwable $uploadError) {
                        $this->flash('Erro no upload do logo: ' . $uploadError->getMessage(), 'error');
                        header('Location: content.php');
                        exit;
                    }
                }
            }

            if (isset($_POST['top_links'])) { $content['site']['top_links'] = post_list('top_links', ['label', 'url']); }
            if (isset($_POST['main_menu'])) { $content['site']['main_menu'] = post_list('main_menu', ['label', 'url']); }
            if (isset($_POST['footer_ctas'])) { $content['site']['footer_ctas'] = post_list('footer_ctas', ['label', 'url']); }
            if (isset($_POST['addresses'])) { $content['site']['addresses'] = post_list('addresses', ['title', 'description', 'url']); }
            $content['why_univag']['title'] = trim((string) ($_POST['why_title'] ?? $content['why_univag']['title']));
            if (isset($_POST['why_cards'])) { $content['why_univag']['cards'] = post_list('why_cards', ['title', 'slug', 'image', 'icon']); }
            $content['courses']['title'] = trim((string) ($_POST['courses_title'] ?? $content['courses']['title']));
            if (isset($_POST['course_items'])) { $content['courses']['items'] = post_tags_list('course_items'); }
            $content['modalities']['title'] = trim((string) ($_POST['modalities_title'] ?? $content['modalities']['title']));
            if (isset($_POST['modalities_items'])) { $content['modalities']['items'] = post_list('modalities_items', ['title', 'image', 'content', 'cta_label', 'cta_url']); }
            $content['scholarships']['title']       = trim((string) ($_POST['scholarships_title'] ?? $content['scholarships']['title']));
            $content['scholarships']['cta_label']   = trim((string) ($_POST['scholarships_cta_label'] ?? ''));
            $content['scholarships']['cta_url']     = trim((string) ($_POST['scholarships_cta_url'] ?? ''));
            $allowedShapes = ['diagonal', 'retangulo', 'quadrado', 'arredondado'];
            $shape = (string) ($_POST['scholarships_cover_shape'] ?? 'diagonal');
            $content['scholarships']['cover_shape'] = in_array($shape, $allowedShapes, true) ? $shape : 'diagonal';

            // Handle cover image: file upload takes priority over URL field
            if (!empty($_FILES['scholarships_cover_image']['name'])) {
                try {
                    $this->siteImageModel->saveUpload($_FILES['scholarships_cover_image'], 'scholarships_cover');
                    // Clear the URL so the DB image is used
                    $content['scholarships']['cover_image'] = '';
                } catch (\Throwable $uploadError) {
                    $this->flash('Erro no upload da imagem: ' . $uploadError->getMessage(), 'error');
                    header('Location: content.php');
                    exit;
                }
            } else {
                $content['scholarships']['cover_image'] = trim((string) ($_POST['scholarships_cover_image'] ?? ''));
            }
            if (isset($_POST['scholarship_items'])) { $content['scholarships']['items'] = post_list('scholarship_items', ['label', 'url']); }
            $content['structure']['title'] = trim((string) ($_POST['structure_title'] ?? $content['structure']['title']));
            $content['structure']['cta_label'] = trim((string) ($_POST['structure_cta_label'] ?? $content['structure']['cta_label']));
            $content['structure']['cta_url'] = trim((string) ($_POST['structure_cta_url'] ?? $content['structure']['cta_url']));
            $content['news']['title'] = trim((string) ($_POST['news_title'] ?? $content['news']['title']));
            if (isset($_POST['featured_title']) || isset($_POST['featured_date']) || isset($_POST['featured_excerpt']) || isset($_POST['featured_image']) || isset($_POST['featured_url'])) {
                $content['news']['featured'] = [
                'title' => trim((string) ($_POST['featured_title'] ?? '')),
                'date' => trim((string) ($_POST['featured_date'] ?? '')),
                'excerpt' => trim((string) ($_POST['featured_excerpt'] ?? '')),
                'image' => trim((string) ($_POST['featured_image'] ?? '')),
                'url' => trim((string) ($_POST['featured_url'] ?? '')),
                ];
            }
            if (isset($_POST['news_items'])) { $content['news']['items'] = post_list('news_items', ['title', 'date', 'image', 'url']); }

            $footerColumns = [];
            foreach (($_POST['footer_columns'] ?? []) as $column) {
                $title = trim((string) ($column['title'] ?? ''));
                if ($title === '') {
                    continue;
                }
                $links = [];
                foreach (($column['links'] ?? []) as $link) {
                    $label = trim((string) ($link['label'] ?? ''));
                    if ($label !== '') {
                        $links[] = ['label' => $label, 'url' => trim((string) ($link['url'] ?? ''))];
                    }
                }
                $footerColumns[] = ['title' => $title, 'links' => $links];
            }
            if (isset($_POST['footer_columns'])) { $content['site']['footer_columns'] = $footerColumns; }

            $pages = $content['pages'];
            foreach (($_POST['pages'] ?? []) as $slug => $page) {
                $pageData = ['title' => trim((string) ($page['title'] ?? '')), 'intro' => trim((string) ($page['intro'] ?? '')), 'blocks' => []];
                foreach (($page['blocks'] ?? []) as $block) {
                    $blockTitle = trim((string) ($block['title'] ?? ''));
                    if ($blockTitle !== '') {
                        $pageData['blocks'][] = ['title' => $blockTitle, 'content' => trim((string) ($block['content'] ?? ''))];
                    }
                }
                $pages[$slug] = $pageData;
            }
            $content['pages'] = $pages;
            $this->contentModel->save($content);
            $this->flash('Conteudo do site atualizado com sucesso.');
            header('Location: content.php');
            exit;
        }

        View::render('admin.content', [
            'title' => 'Conteudo do Site',
            'active' => 'content',
            'flash' => $this->consumeFlash(),
            'content' => $content,
        ]);
    }

    public function manuals(): void
    {
        require_login();
        $manualModel = new \App\Models\ManualModel();
        $action      = trim((string) ($_REQUEST['action'] ?? ''));
        $category    = trim((string) ($_REQUEST['category'] ?? ''));
        $id          = (int) ($_REQUEST['id'] ?? 0);

        // ── POST actions ──────────────────────────────────────────────────
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();

            if ($action === 'save_config') {
                $manualModel->savePageConfig([
                    'title'      => trim((string) ($_POST['title'] ?? '')),
                    'intro'      => trim((string) ($_POST['intro'] ?? '')),
                    'hero_image' => trim((string) ($_POST['hero_image'] ?? '')),
                    'hero_logo'  => trim((string) ($_POST['hero_logo'] ?? '')),
                ]);
                foreach (['aluno', 'professor'] as $cat) {
                    $manualModel->saveCategoryConfig($cat, [
                        'title'         => trim((string) ($_POST['categories'][$cat]['title'] ?? '')),
                        'manuals_title' => trim((string) ($_POST['categories'][$cat]['manuals_title'] ?? '')),
                        'videos_title'  => trim((string) ($_POST['categories'][$cat]['videos_title'] ?? '')),
                    ]);
                    $videos = [];
                    foreach (($_POST['categories'][$cat]['videos'] ?? []) as $v) {
                        $t = trim((string) ($v['title'] ?? ''));
                        if ($t !== '') {
                            $videos[] = ['title' => $t, 'video_url' => trim((string) ($v['video_url'] ?? ''))];
                        }
                    }
                    $manualModel->saveVideos($cat, $videos);
                }
                $this->flash('Configurações salvas com sucesso.');
                header('Location: manuals.php');
                exit;
            }

            if ($action === 'add' && in_array($category, ['aluno', 'professor'], true)) {
                try {
                    $manualModel->create([
                        'category'    => $category,
                        'title'       => trim((string) ($_POST['title'] ?? '')),
                        'description' => trim((string) ($_POST['description'] ?? '')),
                        'media_type'  => in_array($_POST['media_type'] ?? '', ['pdf', 'video'], true) ? $_POST['media_type'] : 'pdf',
                        'pdf_url'     => trim((string) ($_POST['pdf_url'] ?? '')),
                        'video_url'   => trim((string) ($_POST['video_url'] ?? '')),
                        'sort_order'  => 0,
                    ], $_FILES['image'] ?? null, $_FILES['pdf'] ?? null);
                    $this->flash('Manual lançado com sucesso.');
                } catch (\Throwable $e) {
                    $this->flash('Erro ao salvar manual: ' . $e->getMessage(), 'error');
                }
                header('Location: manuals.php');
                exit;
            }

            if ($action === 'edit' && $id > 0) {
                try {
                    $manualModel->update($id, [
                        'title'       => trim((string) ($_POST['title'] ?? '')),
                        'description' => trim((string) ($_POST['description'] ?? '')),
                        'media_type'  => in_array($_POST['media_type'] ?? '', ['pdf', 'video'], true) ? $_POST['media_type'] : 'pdf',
                        'pdf_url'     => trim((string) ($_POST['pdf_url'] ?? '')),
                        'video_url'   => trim((string) ($_POST['video_url'] ?? '')),
                        'sort_order'  => (int) ($_POST['sort_order'] ?? 0),
                    ], $_FILES['image'] ?? null, $_FILES['pdf'] ?? null);
                    $this->flash('Manual atualizado com sucesso.');
                } catch (\Throwable $e) {
                    $this->flash('Erro ao atualizar manual: ' . $e->getMessage(), 'error');
                }
                header('Location: manuals.php');
                exit;
            }

            if ($action === 'delete' && $id > 0) {
                $manualModel->delete($id);
                $this->flash('Manual removido.');
                header('Location: manuals.php');
                exit;
            }

            if ($action === 'delete_video' && $id > 0) {
                $manualModel->deleteVideo($id);
                $this->flash('Vídeo removido.');
                header('Location: manuals.php');
                exit;
            }
        }

        // ── GET: show edit form ───────────────────────────────────────────
        $editManual = null;
        if ($action === 'edit' && $id > 0) {
            $editManual = $manualModel->getById($id);
        }

        View::render('admin.manuals', [
            'title'       => 'Manuais',
            'active'      => 'manuals',
            'flash'       => $this->consumeFlash(),
            'pageConfig'  => $manualModel->getPageConfig(),
            'categories'  => $manualModel->getCategories(),
            'manualsByCategory' => [
                'aluno'     => $manualModel->getByCategory('aluno'),
                'professor' => $manualModel->getByCategory('professor'),
            ],
            'videosByCategory' => [
                'aluno'     => $manualModel->getVideos('aluno'),
                'professor' => $manualModel->getVideos('professor'),
            ],
            'editManual'  => $editManual,
            'action'      => $action,
            'manualModel' => $manualModel,
        ]);
    }

    public function settings(): void
    {
        require_login();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            $current = (string) ($_POST['current_password'] ?? '');
            $new = (string) ($_POST['new_password'] ?? '');
            $confirm = (string) ($_POST['confirm_password'] ?? '');
            $user = (string) ($_SESSION['admin_user'] ?? 'admin');

            if (!$this->userModel->verify($user, $current)) {
                $this->flash('Senha atual invalida.', 'error');
            } elseif ($new === '' || strlen($new) < 6) {
                $this->flash('A nova senha precisa ter no minimo 6 caracteres.', 'error');
            } elseif ($new !== $confirm) {
                $this->flash('A confirmacao de senha nao confere.', 'error');
            } else {
                $this->userModel->savePassword($user, $new);
                $this->flash('Senha alterada com sucesso.');
            }

            header('Location: settings.php');
            exit;
        }

        $content = $this->contentModel->getAll();
        View::render('admin.settings', [
            'title' => 'Configuracoes',
            'active' => 'settings',
            'flash' => $this->consumeFlash(),
            'slides' => $this->sortedSlides($content),
            'content' => $content,
        ]);
    }

    private function flash(string $message, string $type = 'success'): void
    {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
    }

    private function consumeFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return is_array($flash) ? $flash : null;
    }

    private function sortedSlides(array $content): array
    {
        $slides = $content['hero']['slides'] ?? [];
        usort($slides, static fn(array $a, array $b): int => ((int) ($a['order'] ?? 0)) <=> ((int) ($b['order'] ?? 0)));
        return $slides;
    }

    private function findSlide(array $content, string $id): ?array
    {
        foreach (($content['hero']['slides'] ?? []) as $slide) {
            if (($slide['id'] ?? '') === $id) {
                return $slide;
            }
        }
        return null;
    }

    private function replaceSlide(array &$content, string $id, array $newSlide): void
    {
        foreach (($content['hero']['slides'] ?? []) as $index => $slide) {
            if (($slide['id'] ?? '') === $id) {
                $content['hero']['slides'][$index] = $newSlide;
                return;
            }
        }
    }

    private function deleteSlide(array &$content, string $id): void
    {
        $slides = $content['hero']['slides'] ?? [];
        foreach ($slides as $index => $slide) {
            if (($slide['id'] ?? '') === $id) {
                array_splice($slides, $index, 1);
                $content['hero']['slides'] = $slides;
                return;
            }
        }
    }

    private function normalizeSlide(array $data, ?array $existing = null): array
    {
        $slide = $existing ?? [];
        $slide['id']                   = $existing['id'] ?? bin2hex(random_bytes(6));
        $slide['image']                = trim((string) ($data['image'] ?? ($existing['image'] ?? '')));
        $slide['cta_label']            = trim((string) ($data['cta_label'] ?? ''));
        $slide['cta_url']              = trim((string) ($data['cta_url'] ?? ''));
        $slide['secondary_cta_label']  = trim((string) ($data['secondary_cta_label'] ?? ''));
        $slide['secondary_cta_url']    = trim((string) ($data['secondary_cta_url'] ?? ''));
        $slide['active']               = !empty($data['active']);
        $slide['order']                = (int) ($data['order'] ?? 0);
        return $slide;
    }

    private function uploadImage(array $file, string $folder = 'slides'): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return '';
        }

        $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $allowed, true)) {
            throw new \RuntimeException('Formato de imagem invalido.');
        }

        $targetDir = MEDIA_PATH . '/' . $folder;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $filename = bin2hex(random_bytes(6)) . '.' . $ext;
        $target = $targetDir . '/' . $filename;
        if (!move_uploaded_file((string) $file['tmp_name'], $target)) {
            throw new \RuntimeException('Falha ao salvar a imagem enviada.');
        }

        return 'media/' . $folder . '/' . $filename;
    }
}
