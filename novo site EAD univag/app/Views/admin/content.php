<?php require BASE_PATH . '/app/Views/admin/partials/header.php'; ?>
<?php if ($flash): ?><div class="admin-alert admin-alert--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<h1 class="admin-page-title">Conteudo do site</h1>
<form method="post">
    <section class="admin-panel">
        <div class="admin-panel__header"><h2>Configuracoes gerais</h2></div>
        <div class="admin-form-grid">
            <div class="admin-field"><label>Nome do site</label><input type="text" name="site_name" value="<?= e($content['site']['site_name']) ?>"></div>
            <div class="admin-field"><label>Texto da marca</label><input type="text" name="logo_text" value="<?= e($content['site']['logo_text']) ?>"></div>
            <div class="admin-field"><label>Texto da privacidade</label><input type="text" name="privacy_text" value="<?= e($content['site']['privacy_text']) ?>"></div>
            <div class="admin-field"><label>Link da privacidade</label><input type="text" name="privacy_url" value="<?= e($content['site']['privacy_url']) ?>"></div>
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
    <div class="admin-actions-row"><button type="submit" class="admin-btn admin-btn--primary">Salvar conteudo</button></div>
</form>
<?php require BASE_PATH . '/app/Views/admin/partials/footer.php'; ?>
