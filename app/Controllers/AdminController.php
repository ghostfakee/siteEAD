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
            $content['site']['site_name']       = trim((string) ($_POST['site_name']       ?? $content['site']['site_name']));
            $content['site']['logo_text']       = trim((string) ($_POST['logo_text']       ?? $content['site']['logo_text']));
            $content['site']['privacy_text']    = trim((string) ($_POST['privacy_text']    ?? $content['site']['privacy_text']));
            $content['site']['privacy_url']     = trim((string) ($_POST['privacy_url']     ?? $content['site']['privacy_url']));
            $content['site']['whatsapp_number'] = preg_replace('/\D/', '', (string) ($_POST['whatsapp_number'] ?? ''));

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
            // Handle bolsas banner image upload
            if (!empty($_FILES['scholarships_cover_image']['name'])) {
                try {
                    $this->siteImageModel->saveUpload($_FILES['scholarships_cover_image'], 'scholarships_cover');
                } catch (\Throwable $uploadError) {
                    $this->flash('Erro no upload da imagem: ' . $uploadError->getMessage(), 'error');
                    header('Location: content.php');
                    exit;
                }
            }
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
                if (!empty($_FILES['manuals_banner']['name'])) {
                    try {
                        $this->siteImageModel->saveUpload($_FILES['manuals_banner'], 'manuals_banner');
                    } catch (\Throwable $uploadError) {
                        $this->flash('Erro no upload do banner: ' . $uploadError->getMessage(), 'error');
                        header('Location: manuals.php');
                        exit;
                    }
                }
                $manualModel->savePageConfig([
                    'title' => trim((string) ($_POST['title'] ?? '')),
                    'intro' => trim((string) ($_POST['intro'] ?? '')),
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

    public function modalities(): void
    {
        require_login();
        $modalityModel = new \App\Models\ModalityModel();
        $action        = (string) ($_POST['action'] ?? '');
        $id            = (int) ($_POST['id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            try {
                match ($action) {
                    'create' => $modalityModel->create($_POST, $_FILES['image'] ?? null),
                    'update' => $modalityModel->update($id, $_POST, $_FILES['image'] ?? null),
                    'delete' => $modalityModel->delete($id),
                    default  => throw new \RuntimeException('Ação inválida.'),
                };
                $this->flash(match ($action) {
                    'create' => 'Modalidade criada.',
                    'update' => 'Modalidade atualizada.',
                    'delete' => 'Modalidade excluída.',
                    default  => 'OK.',
                });
            } catch (\Throwable $e) {
                $this->flash($e->getMessage(), 'error');
            }
            header('Location: modalities.php');
            exit;
        }

        $editId = (int) ($_GET['edit'] ?? 0);

        View::render('admin.modalities', [
            'title'         => 'Modalidades',
            'active'        => 'modalities',
            'flash'         => $this->consumeFlash(),
            'modalityList'  => $modalityModel->all(),
            'count'         => $modalityModel->count(),
            'modalityModel' => $modalityModel,
            'editId'        => $editId,
        ]);
    }

    public function news(): void
    {
        require_login();
        $newsModel = new \App\Models\NewsModel();
        $action    = (string) ($_GET['action'] ?? ($_POST['action'] ?? ''));
        $id        = (int) ($_GET['id'] ?? ($_POST['id'] ?? 0));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            try {
                match ($action) {
                    'create' => $newsModel->create($_POST, $_FILES['image'] ?? null),
                    'update' => $newsModel->update($id, $_POST, $_FILES['image'] ?? null),
                    'delete' => $newsModel->delete($id),
                    'feature' => $newsModel->setFeatured($id),
                    default => throw new \RuntimeException('Ação inválida.'),
                };
                $this->flash(match ($action) {
                    'create'  => 'Notícia criada.',
                    'update'  => 'Notícia atualizada.',
                    'delete'  => 'Notícia excluída.',
                    'feature' => 'Notícia marcada como destaque.',
                    default   => 'OK.',
                });
            } catch (\Throwable $e) {
                $this->flash($e->getMessage(), 'error');
            }
            header('Location: news.php');
            exit;
        }

        $editNews = ($action === 'edit' && $id > 0) ? $newsModel->getById($id) : null;

        View::render('admin.news', [
            'title'     => 'Notícias',
            'active'    => 'news',
            'flash'     => $this->consumeFlash(),
            'newsList'  => $newsModel->all(),
            'count'     => $newsModel->count(),
            'newsModel' => $newsModel,
            'editNews'  => $editNews,
        ]);
    }

    public function users(): void
    {
        require_login();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            $action = (string) ($_POST['action'] ?? '');
            $userId = (int) ($_POST['user_id'] ?? 0);

            try {
                match ($action) {
                    'create' => (function () {
                        $pw      = (string) ($_POST['password'] ?? '');
                        $confirm = (string) ($_POST['confirm_password'] ?? '');
                        if ($pw !== $confirm) throw new \RuntimeException('As senhas não conferem.');
                        $this->userModel->create(
                            trim((string) ($_POST['username'] ?? '')),
                            $pw,
                            (string) ($_POST['role'] ?? 'admin')
                        );
                        $this->flash('Usuário criado com sucesso.');
                    })(),

                    'update' => (function () use ($userId) {
                        $this->userModel->update(
                            $userId,
                            trim((string) ($_POST['username'] ?? '')),
                            (string) ($_POST['role'] ?? 'admin'),
                            (bool) ($_POST['active'] ?? true)
                        );
                        $this->flash('Usuário atualizado.');
                    })(),

                    'reset_password' => (function () use ($userId) {
                        $pw      = (string) ($_POST['new_password'] ?? '');
                        $confirm = (string) ($_POST['confirm_password'] ?? '');
                        if ($pw !== $confirm) throw new \RuntimeException('As senhas não conferem.');
                        $this->userModel->resetPassword($userId, $pw);
                        $this->flash('Senha resetada com sucesso.');
                    })(),

                    'delete' => (function () use ($userId) {
                        $this->userModel->delete($userId);
                        $this->flash('Usuário excluído.');
                    })(),

                    default => throw new \RuntimeException('Ação inválida.'),
                };
            } catch (\Throwable $e) {
                $this->flash($e->getMessage(), 'error');
            }

            header('Location: users.php');
            exit;
        }

        View::render('admin.users', [
            'title'  => 'Usuários do CMS',
            'active' => 'users',
            'flash'  => $this->consumeFlash(),
            'users'  => $this->userModel->all(),
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

            if (!$this->userModel->verifyByUsername($user, $current)) {
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
