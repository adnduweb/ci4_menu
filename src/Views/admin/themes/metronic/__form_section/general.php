<div class="row">
    <label class="col-xl-3"></label>
    <div class="col-lg-9 col-xl-6">
        <h3 class="kt-section__title kt-section__title-sm"><?= lang('Core.info_menu'); ?>:</h3>
    </div>
</div>


<div class="form-group row">
    <label for="name" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.name')); ?>* : </label>
    <div class="col-lg-9 col-xl-6">
        <input class="form-control" required type="text" value="<?= old('name') ? old('name') : $form->name; ?>" name="name" id="name">
        <div class="invalid-feedback"><?= lang('Core.this_field_is_requis'); ?> </div>
    </div>
</div>

<?php if (!empty($form->id)) { ?> <?= form_hidden('id', $form->id); ?> <?php } ?>