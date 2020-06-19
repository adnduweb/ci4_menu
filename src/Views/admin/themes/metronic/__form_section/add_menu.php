    <div id="edit_menu"></div>

    <?= form_open('', ['id' => 'menu-add', 'class' => 'kt-form kt-section', 'novalidate' => false, 'style' => 'margin-top:30px;']); ?>
    <h3> <?= lang('Core.add_menu_perso'); ?> </h3>
    <div class="kt-section__content kt-section__content--solid">

        <div class="form-group">
            <label for="addInputName"><?= lang('Core.name'); ?></label>
            <!-- <input type="text" name="name" class="form-control" id="addInputName" placeholder="<?= lang('Core.item-name'); ?>" required> -->
            <?= form_input_spread('name', null, 'id="name" class="form-control lang"', 'text', true); ?>
        </div>

        <div class="form-group">
            <label for="addInputSlug"><?= lang('Core.slug'); ?> &nbsp;</label>
            <?= form_input_spread('slug', null, 'id="slug" class="form-control lang"', 'text', true); ?>
        </div>

        <div class="form-group">
            <label for="id"><?= ucfirst(lang('Core.menu_item')); ?></label>
            <select required name="menu_main_id" class="form-control selectpicker file kt-selectpicker" data-actions-box="true" title="<?= ucfirst(lang('Core.choose_one_of_the_following')); ?>" id="id">
                <?php foreach ($menu_items as $item) { ?>
                    <option value="<?= $item->id; ?>"><?= $item->name; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <input type="hidden" name="menu_main_id" value="<?= $menu_item->id; ?>" />
    <input type="hidden" name="depth" value="0" />
    <input type="hidden" name="left" value="0" />
    <input type="hidden" name="right" value="0" />
    <input type="hidden" name="add_form" value="1" />

    <button class="btn btn-primary btn-sm" id="addButton"> <i class="fa fa-plus-circle" aria-hidden="true"></i> <?= lang('Core.add_menu'); ?></button>
    <?= form_close(); ?>