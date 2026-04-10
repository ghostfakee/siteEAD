<?php

declare(strict_types=1);

function default_content(): array
{
    return [
        'site' => [
            'site_name' => 'UNIVAG EAD',
            'logo_text' => 'UNIVAG',
            'top_links' => [
                ['label' => '0800 646 1210', 'url' => 'tel:08006461210'],
                ['label' => '+55 (65) 3688-6124', 'url' => 'tel:+556536886124'],
                ['label' => '(65) 3688-6054', 'url' => 'tel:+556536886054'],
                ['label' => 'Biblioteca', 'url' => '#'],
                ['label' => 'Ouvidoria', 'url' => '#'],
            ],
            'main_menu' => [
                ['label' => 'Cursos', 'url' => '#cursos'],
                ['label' => 'Processo Seletivo', 'url' => '#'],
                ['label' => 'Bolsas e Parcelamento', 'url' => '#bolsas'],
                ['label' => 'Manuais', 'url' => 'manuais/index.php'],
                ['label' => 'Pesquisa e Extensão', 'url' => '#noticias'],
                ['label' => 'Sobre o UNIVAG', 'url' => '#porque-univag'],
                ['label' => 'Eventos', 'url' => '#noticias'],
            ],
            'footer_ctas' => [
                ['label' => 'Consulta e-MEC', 'url' => '#'],
                ['label' => 'Fale Conosco', 'url' => '#'],
                ['label' => 'Manual do Aluno', 'url' => 'manuais/index.php'],
                ['label' => 'Relatório de Equidade Salarial', 'url' => '#'],
            ],
            'footer_columns' => [
                ['title' => 'Vestibulando', 'links' => [['label' => 'Cursos', 'url' => '#cursos'], ['label' => 'Bolsas', 'url' => '#bolsas']]],
                ['title' => 'Aluno', 'links' => [['label' => 'Portal do Aluno', 'url' => '#'], ['label' => 'Manual do Aluno', 'url' => 'manuais/index.php']]],
                ['title' => 'UNIVAG', 'links' => [['label' => 'Quem Somos', 'url' => '#porque-univag'], ['label' => 'Estrutura', 'url' => 'page.php?slug=campus']]],
            ],
            'addresses' => [
                ['title' => 'Campus - Várzea Grande', 'description' => "Unidade: Sede\nAv. Dom Orlando Chaves, 2655\nVárzea Grande - MT", 'url' => '#'],
                ['title' => 'Campus - Cuiabá', 'description' => "Unidade: Morada do Ouro\nAv. Mário Augusto Vieira, 269\nCuiabá - MT", 'url' => '#'],
            ],
            'privacy_text' => 'Política de Privacidade',
            'privacy_url' => '#',
        ],
        'hero' => [
            'slides' => [
                ['title' => 'Super Aulas ENEM', 'subtitle' => 'Carrossel controlado pelo CMS, com troca manual e automática.', 'image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1400&q=80', 'cta_label' => 'Inscrições abertas', 'cta_url' => '#', 'badge' => 'Presencial e Online', 'info_title' => 'Informações', 'info_lines' => ['(65) 3388-5700', '27/SET | Auditório V', '08h às 18h']],
                ['title' => 'Vestibular 2026', 'subtitle' => 'Atualize links, textos e imagens do banner sem mexer no código.', 'image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1400&q=80', 'cta_label' => 'Quero participar', 'cta_url' => '#', 'badge' => 'Graduação e Pós', 'info_title' => 'Atendimento', 'info_lines' => ['0800 646 1210', 'Várzea Grande e Cuiabá', 'Modalidades flexíveis']],
            ],
        ],
        'why_univag' => [
            'title' => 'Por que ser Univag',
            'cards' => [
                ['title' => 'Prêmios e Selos', 'slug' => 'premios-e-selos', 'image' => 'https://images.unsplash.com/photo-1567427018141-0584cfcbf1b8?auto=format&fit=crop&w=800&q=80', 'icon' => '★'],
                ['title' => 'Notas e Posicionamento', 'slug' => 'notas-e-posicionamento', 'image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=800&q=80', 'icon' => '№'],
                ['title' => 'Manuais', 'slug' => 'manuais', 'image' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=800&q=80', 'icon' => '▶'],
                ['title' => 'Campus (Estrutura)', 'slug' => 'campus', 'image' => 'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=800&q=80', 'icon' => '⌂'],
            ],
        ],
        'courses' => [
            'title' => 'Cursos',
            'items' => [
                ['title' => 'Graduação', 'image' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1200&q=80', 'tags' => ['Presencial', 'Ao Vivo', 'Digital'], 'url' => '#'],
                ['title' => 'Pós-Graduação', 'image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80', 'tags' => ['Especialização', 'Mestrado', 'MBA'], 'url' => '#'],
                ['title' => 'Extensão', 'image' => 'https://images.unsplash.com/photo-1529390079861-591de354faf5?auto=format&fit=crop&w=1200&q=80', 'tags' => ['Curta duração'], 'url' => '#'],
                ['title' => 'UNIVAG Idiomas', 'image' => 'https://images.unsplash.com/photo-1513258496099-48168024aec0?auto=format&fit=crop&w=1200&q=80', 'tags' => ['Inglês', 'Português', 'Espanhol', 'Francês'], 'url' => '#'],
            ],
        ],
        'modalities' => [
            'title' => 'Modalidades',
            'items' => [
                ['title' => 'Presencial', 'image' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1200&q=80', 'content' => "Aulas práticas e teóricas com presença em campus.\nPode alterar título, link, texto e imagem no CMS.", 'cta_label' => 'Saiba mais', 'cta_url' => '#'],
                ['title' => 'Ao Vivo', 'image' => 'https://images.unsplash.com/photo-1584697964190-a1a0f0f0fcd4?auto=format&fit=crop&w=1200&q=80', 'content' => "Aulas síncronas com interação em tempo real.\nPode trocar por qualquer modalidade nova.", 'cta_label' => 'Ver detalhes', 'cta_url' => '#'],
                ['title' => 'Digital', 'image' => 'https://images.unsplash.com/photo-1516321497487-e288fb19713f?auto=format&fit=crop&w=1200&q=80', 'content' => "Conteúdo online com flexibilidade.\nEstrutura pronta para expansão.", 'cta_label' => 'Conhecer', 'cta_url' => '#'],
            ],
        ],
        'scholarships' => ['title' => 'Bolsas e Parcelamentos', 'items' => [['label' => 'NBB', 'url' => '#'], ['label' => 'PIE Univag', 'url' => '#'], ['label' => 'UNIVAG+', 'url' => '#'], ['label' => 'Convênio com Empresas', 'url' => '#'], ['label' => 'Orgulho de Ser Univag', 'url' => '#'], ['label' => 'FIES', 'url' => '#']]],
        'structure' => ['title' => 'Conheça nossa estrutura', 'cta_label' => 'Ver campus', 'cta_url' => 'page.php?slug=campus'],
        'news' => [
            'title' => 'Acontece no Univag',
            'featured' => ['title' => 'Hackamed 2025 conecta saúde, tecnologia e negócios', 'date' => '2025-09-16', 'excerpt' => 'Bloco de destaque da home gerenciável pelo CMS.', 'image' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=1200&q=80', 'url' => '#'],
            'items' => [
                ['title' => 'Medicina UNIVAG abre inscrições com seleção pela nota do Enem', 'date' => '2025-05-29', 'image' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=800&q=80', 'url' => '#'],
                ['title' => 'UNIVAG consolida sua excelência educacional com destaque no Enade', 'date' => '2025-04-22', 'image' => 'https://images.unsplash.com/photo-1519452575417-564c1401ecc0?auto=format&fit=crop&w=800&q=80', 'url' => '#'],
                ['title' => 'Estudantes ganham reforço gratuito com o Super Aulas ENEM', 'date' => '2025-09-17', 'image' => 'https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&w=800&q=80', 'url' => '#'],
                ['title' => 'Mais de 2 mil jovens descobrem carreiras e oportunidades', 'date' => '2025-08-28', 'image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80', 'url' => '#'],
            ],
        ],
        'pages' => [
            'premios-e-selos' => ['title' => 'Prêmios e Selos', 'intro' => 'Página interna em PHP, editável pelo CMS.', 'blocks' => [['title' => 'Reconhecimento acadêmico', 'content' => 'Cadastre selos, certificados, imagens e descrições.']]],
            'notas-e-posicionamento' => ['title' => 'Notas e Posicionamento', 'intro' => 'Rankings, avaliações, notas MEC e destaques.', 'blocks' => [['title' => 'Nota máxima', 'content' => 'Inclua dados, gráficos simples e links externos.']]],
            'manuais' => [
                'title' => 'Manuais e Tutoriais',
                'intro' => 'Selecione a categoria de manuais abaixo.',
                'hero_image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1800&q=80',
                'hero_logo' => '',
                'category_buttons' => [
                    ['key' => 'aluno', 'label' => 'Manual do Aluno'],
                    ['key' => 'professor', 'label' => 'Manual do Professor'],
                ],
                'categories' => [
                    'aluno' => [
                        'title' => 'Manual do Aluno',
                        'manuals_title' => 'Manual do Aluno',
                        'videos_title' => 'Videos Tutoriais',
                        'manuals' => [
                            ['title' => 'Tutorial do Aluno Cursos Digitais', 'description' => '', 'image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=900&q=80', 'pdf_url' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'],
                            ['title' => 'Manual do Aluno Digital', 'description' => '', 'image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=900&q=80', 'pdf_url' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'],
                            ['title' => 'Manual do Aluno Ao Vivo', 'description' => '', 'image' => 'https://images.unsplash.com/photo-1513258496099-48168024aec0?auto=format&fit=crop&w=900&q=80', 'pdf_url' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'],
                        ],
                        'videos' => [
                            ['title' => 'Acessando o AVA', 'video_url' => 'https://player.vimeo.com/video/722285715'],
                            ['title' => 'Baixar Arquivos', 'video_url' => 'https://player.vimeo.com/video/600460557'],
                            ['title' => 'Calendario', 'video_url' => 'https://player.vimeo.com/video/600460606'],
                        ],
                    ],
                    'professor' => [
                        'title' => 'Manual do Professor',
                        'manuals_title' => 'Manual do Professor',
                        'videos_title' => 'Videos Tutoriais',
                        'manuals' => [
                            ['title' => 'Manual do Professor Digital', 'description' => '', 'image' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=900&q=80', 'pdf_url' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'],
                            ['title' => 'Manual do Professor Ao Vivo', 'description' => '', 'image' => 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=900&q=80', 'pdf_url' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'],
                            ['title' => 'Manual do Professor Apoio ao Presencial', 'description' => '', 'image' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=900&q=80', 'pdf_url' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'],
                        ],
                        'videos' => [
                            ['title' => 'AVA Professores', 'video_url' => 'https://player.vimeo.com/video/600462964'],
                            ['title' => 'Capacitacao Docentes Zoom', 'video_url' => 'https://player.vimeo.com/video/600463040'],
                        ],
                    ],
                ],
                'blocks' => [],
            ],
            'campus' => ['title' => 'Campus e Estrutura', 'intro' => 'Galeria, descrições e blocos de estrutura.', 'blocks' => [['title' => 'Laboratórios', 'content' => 'Adicione destaques dos ambientes.'], ['title' => 'Biblioteca', 'content' => 'Mantenha horários, acervo e serviços atualizados.']]],
        ],
    ];
}

function ensure_storage(): void
{
    if (!is_dir(DATA_PATH)) {
        mkdir(DATA_PATH, 0755, true);
    }
    if (!is_dir(MEDIA_PATH)) {
        mkdir(MEDIA_PATH, 0755, true);
    }
    if (!file_exists(CONTENT_FILE)) {
        file_put_contents(CONTENT_FILE, json_encode(default_content(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
    $needsUsers = !file_exists(USERS_FILE);
    if (!$needsUsers) {
        $users = require USERS_FILE;
        $needsUsers = !is_array($users) || $users === [];
    }
    if ($needsUsers) {
        // Argon2id hash — user must change on first login
        $hash = password_hash('admin123', PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost'   => 4,
            'threads'     => 2,
        ]);
        file_put_contents(USERS_FILE, "<?php\n\nreturn [\n    'admin' => '{$hash}',\n];\n");
    }
}

function content_data(): array
{
    ensure_storage();

    // Try MySQL first
    $content = null;
    if (db_available()) {
        try {
            $pdo = get_pdo();
            $stmt = $pdo->query("SELECT store_value FROM content_store WHERE store_key = 'main_content' LIMIT 1");
            $row = $stmt->fetch();
            if ($row && $row['store_value'] !== '') {
                $decoded = json_decode($row['store_value'], true);
                if (is_array($decoded) && $decoded !== []) {
                    $content = $decoded;
                }
            }
        } catch (\Throwable) {
            // fall through to JSON
        }
    }

    // Fallback: read from JSON file
    if ($content === null) {
        $data = json_decode((string) file_get_contents(CONTENT_FILE), true);
        $content = is_array($data) && $data !== [] ? $data : default_content();

        // Seed MySQL if available
        if (db_available()) {
            try {
                $json = json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $pdo = get_pdo();
                $stmt = $pdo->prepare("INSERT INTO content_store (store_key, store_value) VALUES ('main_content', ?) ON DUPLICATE KEY UPDATE store_value = ?");
                $stmt->execute([$json, $json]);
            } catch (\Throwable) { /* silent */ }
        }
    }

    $content['site']['whatsapp_number'] = $content['site']['whatsapp_number'] ?? '';

    foreach (($content['hero']['slides'] ?? []) as $index => $slide) {
        $content['hero']['slides'][$index]['id'] = $slide['id'] ?? ('slide_' . ($index + 1));
        $content['hero']['slides'][$index]['order'] = (int) ($slide['order'] ?? $index);
        $content['hero']['slides'][$index]['active'] = array_key_exists('active', $slide) ? (bool) $slide['active'] : true;
        $content['hero']['slides'][$index]['secondary_cta_label'] = $slide['secondary_cta_label'] ?? '';
        $content['hero']['slides'][$index]['secondary_cta_url'] = $slide['secondary_cta_url'] ?? '';
    }

    foreach (['footer_ctas', 'top_links', 'main_menu'] as $group) {
        foreach (($content['site'][$group] ?? []) as $index => $item) {
            if (($item['url'] ?? '') === 'page.php?slug=manuais') {
                $content['site'][$group][$index]['url'] = 'manuais/index.php';
            }
        }
    }

    $hasManualsMenu = false;
    foreach (($content['site']['main_menu'] ?? []) as $item) {
        if (($item['url'] ?? '') === 'manuais/index.php') {
            $hasManualsMenu = true;
            break;
        }
    }
    if (!$hasManualsMenu) {
        array_splice($content['site']['main_menu'], 3, 0, [[
            'label' => 'Manuais',
            'url' => 'manuais/index.php',
        ]]);
    }

    foreach (($content['site']['footer_columns'] ?? []) as $columnIndex => $column) {
        foreach (($column['links'] ?? []) as $linkIndex => $link) {
            if (($link['url'] ?? '') === 'page.php?slug=manuais') {
                $content['site']['footer_columns'][$columnIndex]['links'][$linkIndex]['url'] = 'manuais/index.php';
            }
        }
    }

    if (isset($content['pages']['manuais'])) {
        $manualsPage = &$content['pages']['manuais'];
        $manualsPage['hero_image'] = $manualsPage['hero_image'] ?? '';
        $manualsPage['hero_logo'] = $manualsPage['hero_logo'] ?? '';
        $manualsPage['category_buttons'] = $manualsPage['category_buttons'] ?? [
            ['key' => 'aluno', 'label' => 'Manual do Aluno'],
            ['key' => 'professor', 'label' => 'Manual do Professor'],
        ];
        $manualsPage['categories'] = $manualsPage['categories'] ?? [];
    }

    return $content;
}

function save_content(array $content): void
{
    ensure_storage();
    $json = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    // Save to MySQL
    if (db_available()) {
        try {
            $pdo = get_pdo();
            $stmt = $pdo->prepare("INSERT INTO content_store (store_key, store_value) VALUES ('main_content', ?) ON DUPLICATE KEY UPDATE store_value = ?");
            $stmt->execute([$json, $json]);
        } catch (\Throwable) { /* fall through */ }
    }

    // Always save JSON as backup
    file_put_contents(CONTENT_FILE, $json);
}

function users_data(): array
{
    ensure_storage();
    $users = require USERS_FILE;
    return is_array($users) ? $users : [];
}
