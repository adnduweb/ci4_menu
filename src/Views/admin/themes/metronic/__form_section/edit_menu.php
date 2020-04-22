    <?= form_open('', ['id' => 'menu-editor', 'class' => 'kt-form kt-section', 'novalidate' => false, 'style' => 'margin-top:30px;']); ?>
    <h3> <?= lang('Core.editing_menu_custom'); ?> </h3>
    <div class="kt-section__content kt-section__content--solid">
        <div class="form-group">
            <label for="addInputName"><?= lang('Core.name'); ?></label>
            <?= form_input_spread('name', $form->_prepareLang(), 'id="name" class="form-control lang"', 'text', true); ?>
        </div>

        <?php if (is_null($form->id_module)) { ?>
            <div class="form-group">
                <label for="addInputSlug"><?= lang('Core.slug'); ?></label>
                <input type="text" name="slug" value="<?= $form->slug; ?>" class="form-control" id="editInputSlug" placeholder="<?= lang('Core.item-slug'); ?>">
            </div>

            <div class="form-group">
                <label for="id_menu_item"><?= ucfirst(lang('Core.menu_item')); ?></label>
                <select required name="id_menu_item" class="form-control selectpicker file kt-selectpicker" data-actions-box="true" title="<?= ucfirst(lang('Core.choose_one_of_the_following')); ?>" id="id_menu_item">
                    <?php foreach ($menu_items as $item) { ?>
                        <?php if ($item->id_menu_item == $form->id_menu_item) { ?>
                            <option selected value="<?= $item->id_menu_item; ?>"><?= $item->name; ?></option>
                        <?php } else { ?>
                            <option value="<?= $item->id_menu_item; ?>"><?= $item->name; ?></option>
                        <?php } ?>

                    <?php } ?>
                </select>
            </div>
        <?php } else { ?>
            <input type="hidden" name="slug" value="<?= $form->slug; ?>" />
            <input type="hidden" name="id_menu_item" value="<?= $form->id_menu_item; ?>" />
        <?php } ?>
    </div>

    <input type="hidden" name="id" value="<?= $form->id; ?>" />
    <input type="hidden" name="depth" value="<?= $form->depth; ?>" />
    <input type="hidden" name="left" value="<?= $form->left; ?>" />
    <input type="hidden" name="right" value="<?= $form->right; ?>" />
    <input type="hidden" name="edit_form" value="1" />
    <button class="btn btn-info" id="editButton"> <i class="fa fa-edit" aria-hidden="true"></i> <?= lang('Core.edit_menu'); ?></button>
    <?= form_close(); ?>