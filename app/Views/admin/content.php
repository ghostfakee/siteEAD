<?php require BASE_PATH . '/app/Views/admin/partials/header.php'; ?>
<?php if ($flash): ?><div class="admin-alert admin-alert--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<h1 class="admin-page-title">Conteudo do site</h1>
<form method="post" enctype="multipart/form-data">
    <section class="admin-panel">
        <div class="admin-panel__header"><h2>Configuracoes gerais</h2></div>
        <div class="admin-form-grid">
            <div class="admin-field"><label>Nome do site</label><input type="text" name="site_name" value="<?= e($content['site']['site_name']) ?>"></div>
            <div class="admin-field"><label>Texto da marca <small style="font-weight:400;opacity:.6;">(fallback se não houver logo)</small></label><input type="text" name="logo_text" value="<?= e($content['site']['logo_text']) ?>"></div>
            <div class="admin-field"><label>Texto da privacidade</label><input type="text" name="privacy_text" value="<?= e($content['site']['privacy_text']) ?>"></div>
            <div class="admin-field"><label>Link da privacidade</label><input type="text" name="privacy_url" value="<?= e($content['site']['privacy_url']) ?>"></div>
        </div>
    </section>

    <!-- ── Logos ──────────────────────────────────────────────────── -->
    <section class="admin-panel">
        <div class="admin-panel__header">
            <h2>Logos</h2>
            <p style="margin:4px 0 0; font-size:.88rem; opacity:.65;">Upload substitui o texto da marca no header e footer. Use SVG ou PNG com fundo transparente.</p>
        </div>
        <?php $siteImg = new \App\Models\SiteImageModel(); ?>
        <div class="admin-form-grid admin-form-grid--2">
            <div class="admin-field">
                <label>Logo do header <small style="font-weight:400;opacity:.6;">(fundo escuro — use versão branca)</small></label>
                <?php if ($siteImg->exists('logo_nav')): ?>
                    <div style="background:#0d1266; padding:12px; border-radius:8px; margin-bottom:8px; display:inline-block;">
                        <img src="site-image.php?key=logo_nav" style="height:36px; display:block;" alt="logo header atual">
                    </div>
                    <small style="display:block; margin-bottom:6px; opacity:.6;">Logo atual. Novo upload substitui.</small>
                <?php endif; ?>
                <input type="file" name="logo_nav" accept="image/svg+xml,image/png,image/webp,image/jpeg">
            </div>
            <div class="admin-field">
                <label>Logo do footer <small style="font-weight:400;opacity:.6;">(fundo escuro — use versão branca)</small></label>
                <?php if ($siteImg->exists('logo_footer')): ?>
                    <div style="background:#0d1266; padding:12px; border-radius:8px; margin-bottom:8px; display:inline-block;">
                        <img src="site-image.php?key=logo_footer" style="height:48px; display:block;" alt="logo footer atual">
                    </div>
                    <small style="display:block; margin-bottom:6px; opacity:.6;">Logo atual. Novo upload substitui.</small>
                <?php endif; ?>
                <input type="file" name="logo_footer" accept="image/svg+xml,image/png,image/webp,image/jpeg">
            </div>
        </div>
    </section>
    <section class="admin-panel">
        <div class="admin-panel__header"><h2>Header e footer</h2></div>
        <?php foreach (['top_links' => 'Top links', 'main_menu' => 'Menu principal', 'footer_ctas' => 'Chamadas do footer'] as $key => $label): ?>
            <div class="admin-subpanel">
                <h3><?= e($label) ?></h3>
                <?php foreach ($content['site'][$key] as $index => $item): ?>
                    <div class="admin-form-grid">
                        <div class="admin-field"><label>Texto</label><input type="text" name="<?= e($key) ?>[<?= $index ?>][label]" value="<?= e($item['label']) ?>"></div>
                        <div class="admin-field"><label>URL</label><input type="text" name="<?= e($key) ?>[<?= $index ?>][url]" value="<?= e($item['url']) ?>"></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </section>
    <section class="admin-panel">
        <div class="admin-panel__header"><h2>Secoes principais</h2></div>
        <div class="admin-field"><label>Titulo "Por que ser Univag"</label><input type="text" name="why_title" value="<?= e($content['why_univag']['title']) ?>"></div>
        <?php foreach ($content['why_univag']['cards'] as $index => $card): ?>
            <div class="admin-form-grid admin-form-grid--4">
                <div class="admin-field"><label>Titulo</label><input type="text" name="why_cards[<?= $index ?>][title]" value="<?= e($card['title']) ?>"></div>
                <div class="admin-field"><label>Slug</label><input type="text" name="why_cards[<?= $index ?>][slug]" value="<?= e($card['slug']) ?>"></div>
                <div class="admin-field"><label>Imagem URL</label><input type="text" name="why_cards[<?= $index ?>][image]" value="<?= e($card['image']) ?>"></div>
                <div class="admin-field"><label>Icone</label><input type="text" name="why_cards[<?= $index ?>][icon]" value="<?= e($card['icon']) ?>"></div>
            </div>
        <?php endforeach; ?>
        <div class="admin-field"><label>Titulo Cursos</label><input type="text" name="courses_title" value="<?= e($content['courses']['title']) ?>"></div>
        <?php foreach ($content['courses']['items'] as $index => $item): ?>
            <div class="admin-form-grid admin-form-grid--4">
                <div class="admin-field"><label>Titulo</label><input type="text" name="course_items[<?= $index ?>][title]" value="<?= e($item['title']) ?>"></div>
                <div class="admin-field"><label>Imagem URL</label><input type="text" name="course_items[<?= $index ?>][image]" value="<?= e($item['image']) ?>"></div>
                <div class="admin-field"><label>Tags</label><input type="text" name="course_items[<?= $index ?>][tags]" value="<?= e(implode(', ', $item['tags'])) ?>"></div>
                <div class="admin-field"><label>URL</label><input type="text" name="course_items[<?= $index ?>][url]" value="<?= e($item['url']) ?>"></div>
            </div>
        <?php endforeach; ?>
    </section>
    <!-- ── Área 2: Bolsas e Parcelamentos ──────────────────────────── -->
    <section class="admin-panel">
        <div class="admin-panel__header">
            <h2>Área 2 — Bolsas e Parcelamentos</h2>
            <p style="margin:4px 0 0; font-size:.88rem; opacity:.65;">Uma imagem de capa e um título por vez. Configure o botão e os itens de bolsas abaixo.</p>
        </div>

        <div class="admin-form-grid admin-form-grid--2" style="margin-bottom:16px;">
            <div class="admin-field">
                <label>Título da seção</label>
                <input type="text" name="scholarships_title" value="<?= e($content['scholarships']['title']) ?>">
            </div>
            <div class="admin-field">
                <label>Layout da imagem</label>
                <?php $coverShape = $content['scholarships']['cover_shape'] ?? 'diagonal'; ?>
                <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:4px;">
                    <?php foreach ([
                        'diagonal'    => ['label' => 'Diagonal', 'svg' => '<svg width="48" height="36" viewBox="0 0 48 36"><rect width="48" height="36" fill="#d0d0e8" rx="4"/><polygon points="0,0 30,0 0,36" fill="#9090c0"/></svg>'],
                        'retangulo'   => ['label' => 'Retângulo', 'svg' => '<svg width="48" height="36" viewBox="0 0 48 36"><rect width="48" height="36" fill="#9090c0" rx="4"/></svg>'],
                        'quadrado'    => ['label' => 'Quadrado', 'svg' => '<svg width="48" height="36" viewBox="0 0 48 36"><rect x="4" y="0" width="36" height="36" fill="#9090c0" rx="4"/></svg>'],
                        'arredondado' => ['label' => 'Arredondado', 'svg' => '<svg width="48" height="36" viewBox="0 0 48 36"><rect width="48" height="36" fill="#9090c0" rx="18"/></svg>'],
                    ] as $val => $opt): ?>
                        <label style="display:flex; flex-direction:column; align-items:center; gap:4px; cursor:pointer; font-size:.78rem; font-weight:600; opacity:<?= $coverShape === $val ? '1' : '.5' ?>;">
                            <input type="radio" name="scholarships_cover_shape" value="<?= $val ?>" <?= $coverShape === $val ? 'checked' : '' ?> style="display:none;" onchange="this.closest('.admin-field').querySelectorAll('label').forEach(l=>l.style.opacity='.5'); this.closest('label').style.opacity='1'">
                            <?= $opt['svg'] ?>
                            <?= $opt['label'] ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="admin-form-grid admin-form-grid--2" style="margin-bottom:16px;">
            <div class="admin-field">
                <label>Imagem de capa <small style="font-weight:400;opacity:.6;">— upload salva no banco; ou informe uma URL</small></label>
                <?php
                    $hasCoverDb  = (new \App\Models\SiteImageModel())->exists('scholarships_cover');
                    $coverUrl    = $content['scholarships']['cover_image'] ?? '';
                ?>
                <?php if ($hasCoverDb): ?>
                    <div style="margin-bottom:8px;">
                        <img src="site-image.php?key=scholarships_cover" style="max-height:80px; border-radius:8px; object-fit:cover;" alt="capa atual">
                        <small style="display:block; margin-top:4px; opacity:.6;">Imagem salva no banco. Faça novo upload para substituir.</small>
                    </div>
                <?php elseif (!empty($coverUrl)): ?>
                    <div style="margin-bottom:8px;"><img src="<?= e($coverUrl) ?>" style="max-height:80px; border-radius:8px; object-fit:cover;" alt="capa"></div>
                <?php endif; ?>
                <input type="file" name="scholarships_cover_image" accept="image/jpeg,image/png,image/webp,image/gif" style="margin-bottom:6px;">
                <input type="text" name="scholarships_cover_image" value="<?= e($coverUrl) ?>" placeholder="https://... (opcional, usado se não houver upload)">
            </div>
        </div>

        <div class="admin-form-grid admin-form-grid--2" style="margin-bottom:20px; padding:16px; background:#f9f9ff; border-radius:14px; border:1px solid var(--line);">
            <div class="admin-field">
                <label>Texto do botão <small style="font-weight:400;opacity:.6;">(deixe vazio para ocultar)</small></label>
                <input type="text" name="scholarships_cta_label" value="<?= e($content['scholarships']['cta_label'] ?? '') ?>" placeholder="Ex: Veja todas as condições de pagamento">
            </div>
            <div class="admin-field">
                <label>Link do botão</label>
                <input type="text" name="scholarships_cta_url" value="<?= e($content['scholarships']['cta_url'] ?? '') ?>" placeholder="https://...">
            </div>
        </div>

        <h3 style="margin:0 0 12px; font-size:.95rem; opacity:.75;">Itens de Bolsas</h3>
        <?php foreach ($content['scholarships']['items'] as $index => $item): ?>
            <div class="admin-form-grid admin-form-grid--2" style="margin-bottom:8px;">
                <div class="admin-field">
                    <label>Rótulo</label>
                    <input type="text" name="scholarship_items[<?= $index ?>][label]" value="<?= e($item['label']) ?>" placeholder="Ex: Orgulho de Ser UNIVAG">
                </div>
                <div class="admin-field">
                    <label>Link</label>
                    <input type="text" name="scholarship_items[<?= $index ?>][url]" value="<?= e($item['url']) ?>" placeholder="https://...">
                </div>
            </div>
        <?php endforeach; ?>
    </section>

    <div class="admin-actions-row"><button type="submit" class="admin-btn admin-btn--primary">Salvar conteudo</button></div>
</form>
<?php require BASE_PATH . '/app/Views/admin/partials/footer.php'; ?>
