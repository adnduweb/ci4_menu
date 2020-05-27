<?= $this->extend('/admin/themes/metronic/__layouts/layout_1') ?>
<?= $this->section('main') ?>
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <?= $this->include('/admin/themes/metronic/__partials/kt_list_toolbar') ?>
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <span class="kt-portlet__head-icon kt-hidden">
                    <i class="la la-gear"></i>
                </span>
                <h3 class="kt-portlet__head-title">
                    <?php foreach ($menu_items as $item) { ?>
                        <div class="btn-group">
                            <a href="/<?= CI_SITE_AREA; ?>/public/menus/<?= $item->id_menu_item; ?>" class=" btn btn-sm btn-brand">
                                Menu : <?= $item->name; ?>
                            </a>
                            <button type="button" class="btn btn-sm btn-brand dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <ul class="kt-nav">
                                    <?php if ($item->id_menu_item != '1') { ?>
                                        <li class="kt-nav__item">
                                            <a class="kt-nav__link" href="/<?= CI_SITE_AREA; ?>/public/menus/delete/<?= $item->id_menu_item; ?>"><i class="la la-trash"></i> <?= lang('Core.delete'); ?></a>
                                        </li>
                                    <?php } ?>
                                    <li class="kt-nav__item">
                                        <a class="kt-nav__link" href="/<?= CI_SITE_AREA; ?>/public/menus/edit/<?= $item->id_menu_item; ?>"><i class="la la-edit"></i><?= lang('Core.edit'); ?></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    <?php } ?>
                </h3>
            </div>
        </div>
        <div id="app" class="kt-portlet__body">
            <!-- <menu id="nestable-menu">
                <button type="button" class="btn btn-info btn-sm" data-action="expand-all"><?= lang('Core.expand All'); ?></button>
                <button type="button" class="btn btn-info btn-sm" data-action="collapse-all"><?= lang('Core.collapse All'); ?></button>
            </menu> -->
            <div class="row">
                <div class="col-xl-16 col-lg-6 order-lg-3 order-xl-1">
                    <h2 style="margin-bottom:30px;">Menu : <?= $menu_item->name; ?></h2>
                    <div class="dd" id="nestable">
                        <div id="wrapper_nestable">
                            <?= $this->include('\Adnduweb\Ci4_menu\Views\admin\themes\metronic\__form_section\get_menu'); ?>
                        </div>
                    </div>
                    <?php if (inGroups(1, user()->id)) {   ?>
                        <textarea id="nestable-output" rows="3" class="form-control"></textarea>
                    <?php } ?>
                </div>
                <div class="col-xl-6 col-lg-6 order-lg-3 order-xl-1 kt-section">
                    <h2 style="margin-bottom:30px;">Liste des pages disponible</h2>
                    <?php
                    // print_r($modules);
                    if (!empty($modules)) { ?>
                        <?= form_open('', ['id' => 'menu-add-module', 'class' => 'kt-form', 'novalidate' => false, 'style' => 'margin-top:30px;']); ?>
                        <div class="kt-section__content kt-section__content--solid" id="page-tabs">
                            <div class="kt-checkbox-list">
                                <?php foreach ($modules as $k => $v) { ?>
                                    <?php foreach ($v->items as $module) { ?>
                                        <label class="kt-checkbox kt-checkbox--bold">
                                            <input name="page-menu[<?= $v->id_module; ?>][<?= $module->getId(); ?>]" value="<?= base64_encode(serialize($module->getNameAllLang())); ?>" data-method="/" data-id="<?= $module->getId(); ?>" data-module="<?= $k; ?>" type="checkbox" kl_vkbd_parsed="true"> <?= ucfirst($module->getName()); ?>
                                            <input type="hidden" name="id_page" value="<?= $menu_item->id_menu_item; ?>" />
                                            <span></span>
                                        </label>
                                        <input type="hidden" name="type" value="<?= $k ?>" />
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                        <input type="hidden" name="id_menu_item" value="<?= $menu_item->id_menu_item; ?>" />
                        <input type="hidden" name="depth" value="0" />
                        <input type="hidden" name="left" value="0" />
                        <input type="hidden" name="right" value="0" />
                        <button class="btn btn-primary btn-sm" id="addButtonMenu"> <i class="fa fa-plus-circle" aria-hidden="true"></i> <?= lang('Core.add_menu'); ?></button>
                        <?= form_close(); ?>
                    <?php } ?>

                    <?= $this->include('\Adnduweb\Ci4_menu\Views\admin\themes\metronic\__form_section\add_menu'); ?>


                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('extra-js') ?>
<script type="text/javascript">
    (function($) {

        'use strict';

        /*
        Update Output
        */
        var updateOutput = function(e) {
            var list = e.length ? e : $(e.target),
                output = list.data('output');
            if (window.JSON) {
                output.val(window.JSON.stringify(list.nestable('serialize')));
            } else {
                output.val('JSON browser support required for this demo.');
            }

            $.ajax({
                method: "POST",
                url: basePath + segementAdmin + "/sp-admin-ajax",
                data: {
                    ajax: true,
                    controller: "AdminMenusController",
                    action: "sortmenu",
                    value: list.nestable('toArray'),
                    module: window.btoa('Adnduweb/Ci4_menu'),
                },
                responseType: 'json',
                success: function(response, status, xhr) {
                    //Success Message
                    if (xhr.status == 200) {
                        console.log(response);
                        $('#__partialsKtSide').html(response.htmlNav);
                        $.notify({
                            title: _LANG_.updated + "!",
                            message: response.message
                        }, {
                            type: "success",
                            placement: {
                                from: 'bottom',
                                align: 'center'
                            },
                        });
                    }
                }
            });
        }

        var nestableList = $("#nestable > .dd-list");

        /*
        Nestable 1
        */
        $('#nestable').nestable({
            group: 1,
            maxDepth: 3,
            onDragStart: function(event, item, source) {
                //console.log('dragStart', event, item, source);
            },
            beforeDragStop: function(event, item, source) {
                //console.log('beforeDragStop', event, item, source);
            },
            callback: function(l, e, p) {
                updateOutput($('#nestable').data('output', $('#nestable-output')));
            }

        });


        // $('#nestable2').nestable({
        //     group: 1,
        //     maxDepth: 1,
        //     callback: function(l, e, p) {
        //         updateOutput($('#nestable').data('output', $('#nestable-output')));
        //     }
        // });
        // .on('change', updateOutput);
        // updateOutput($('#nestable2').data('output', $('#nestable2-output')));

        $('#nestable-menu').on('click', function(e) {
            var target = $(e.target),
                action = target.data('action');
            if (action === 'expand-all') {
                $('.dd').nestable('expandAll');
            }
            if (action === 'collapse-all') {
                $('.dd').nestable('collapseAll');
            }
        });

        /*************** Add Menu ***************/

        $("#menu-add-module").submit(function(e) {
            e.preventDefault();
            $.ajax({
                method: "POST",
                url: basePath + segementAdmin + "/sp-admin-ajax",
                data: {
                    ajax: true,
                    controller: "AdminMenusController",
                    action: "saveMenu",
                    value: $('#menu-add-module').serialize(),
                    module: window.btoa('Adnduweb/Ci4_menu'),
                },
                responseType: 'json',
                beforeSend: function(xhr, settings) {
                    var params = settings.url.indexOf('?');
                    if (params)
                        settings.url = settings.url + '?time=' + $.now();
                    else
                        //     settings.url = settings.url + '&time=' + $.now();
                        if (env == 'development') {
                            console.log(jqXHR);
                            console.log(settings);
                        }

                    KTApp.block("#nestable", {
                        overlayColor: "#000000",
                        state: "primary"
                    });
                },
                success: function(response, status, xhr) {
                    //Success Message
                    if (xhr.status == 200) {
                        console.log(response);
                        KTApp.unblock("#nestable");
                        $('#wrapper_nestable').html(response.html);
                        $.notify({
                            title: _LANG_.updated + "!",
                            message: response.message
                        }, {
                            type: "success",
                            placement: {
                                from: 'bottom',
                                align: 'center'
                            },
                        });
                    }
                }
            });
        });



        /*************** Add Menu Personnalisé ***************/

        var newIdCount = 1;

        var saveToMenu = function(action) {
            var newName = $("#name").val();
            var newSlug = $("#addInputSlug").val();
            var id_menu_item = $("#id_menu_item").val();
            var newId = 'new-' + newIdCount;

            // if (!newName || !newSlug || $.isNumeric(id_menu_item) == false) {
            //     return false;

            // }

            $.ajax({
                method: "POST",
                url: basePath + segementAdmin + "/sp-admin-ajax",
                data: {
                    ajax: true,
                    controller: "AdminMenusController",
                    action: "saveMenuCustom",
                    value: $('#menu-' + action).serialize(),
                    module: window.btoa('Adnduweb/Ci4_menu'),
                },
                responseType: 'json',
                beforeSend: function(xhr, settings) {

                    var params = settings.url.indexOf('?');
                    if (params)
                        settings.url = settings.url + '?time=' + $.now();
                    else
                        //     settings.url = settings.url + '&time=' + $.now();
                        if (env == 'development') {
                            console.log(jqXHR);
                            console.log(settings);
                        }

                    KTApp.block("#nestable", {
                        overlayColor: "#000000",
                        state: "primary"
                    });
                },
                success: function(response, status, xhr) {
                    //Success Message
                    if (xhr.status == 200) {
                        console.log(response);
                        KTApp.unblock("#nestable");
                        $('#wrapper_nestable').html(response.html);
                        $.notify({
                            title: _LANG_.updated + "!",
                            message: response.message
                        }, {
                            type: "success",
                            placement: {
                                from: 'bottom',
                                align: 'center'
                            },
                        });
                    }
                }
            });
        };

        $(document).on('submit', "#menu-add", function(e) {
            e.preventDefault();
            saveToMenu('add');
        });

        /****** ** ** ** ** * Edit Menu personnalisé ** ** ** ** ** ** ** */

        $(document).on('click', 'a.edit', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            $.ajax({
                method: "POST",
                url: basePath + segementAdmin + "/sp-admin-ajax",
                data: {
                    ajax: true,
                    controller: "AdminMenusController",
                    action: "getMenu",
                    value: id,
                    module: window.btoa('Adnduweb/Ci4_menu'),
                },
                responseType: 'json',
                success: function(response, status, xhr) {
                    //Success Message
                    if (xhr.status == 200) {
                        console.log(response);
                        $('#edit_menu').html(response.html).fadeIn();
                    }
                }
            });
        });

        $(document).on('submit', "#menu-editor", function(e) {
            e.preventDefault();
            saveToMenu('editor');
        });

        /****** ** ** ** ** * Suppression ** ** ** ** ** ** ** */

        $(document).on('click', 'a.delete', function(e) {
            e.preventDefault();
            var id = $(this).data('id');


            swal.fire({
                buttonsStyling: !1,
                text: _LANG_.are_you_sure_delete + " " + id + " " + _LANG_.selected_records + " ?",
                type: "error",
                confirmButtonText: _LANG_.yes_delete + ' !',
                confirmButtonClass: "btn btn-sm btn-bold btn-danger",
                showCancelButton: !0,
                cancelButtonText: _LANG_.no_cancel,
                cancelButtonClass: "btn btn-sm btn-bold btn-brand"
            }).then(function(t) {
                if (t.value) {

                    $.ajax({
                        method: "POST",
                        url: basePath + segementAdmin + "/sp-admin-ajax",
                        data: {
                            ajax: true,
                            controller: "AdminMenusController",
                            action: "deleteMenuItem",
                            value: id,
                            module: window.btoa('Adnduweb/Ci4_menu'),
                        },
                        responseType: 'json',
                        beforeSend: function(xhr, settings) {

                            var params = settings.url.indexOf('?');
                            if (params)
                                settings.url = settings.url + '?time=' + $.now();
                            else
                                //     settings.url = settings.url + '&time=' + $.now();
                                if (env == 'development') {
                                    console.log(jqXHR);
                                    console.log(settings);
                                }

                            KTApp.block("#nestable", {
                                overlayColor: "#000000",
                                state: "primary"
                            });
                        },
                        success: function(result, status, xhr) {
                            //Success Message
                            if (xhr.status == 200) {
                                KTApp.unblock("#nestable");
                                $('#wrapper_nestable').html(result.html);
                                $.notify({
                                    title: _LANG_.deleted + "!",
                                    message: result.message
                                }, {
                                    type: result.type,
                                    placement: {
                                        from: 'bottom',
                                        align: 'center'
                                    },
                                });
                            }
                        }
                    });
                } else {
                    $.notify({
                        title: _LANG_.deleted,
                        message: _LANG_.your_selected_records_have_not_been_deleted
                    }, {
                        type: 'info',
                        placement: {
                            from: 'bottom',
                            align: 'center'
                        },
                    });
                }
            })



            // $.ajax({
            //     method: "POST",
            //     url: basePath + segementAdmin + "/sp-admin-ajax",
            //     data: {
            //         ajax: true,
            //         controller: "AdminMenusController",
            //         action: "deleteMenuItem",
            //         value: id,
            //         module: window.btoa('Adnduweb/Ci4_menu'),
            //     },
            //     responseType: 'json',
            //     success: function(response, status, xhr) {
            //         //Success Message
            //         if (xhr.status == 200) {
            //             console.log(response);
            //             $('#edit_menu').html(response.html).fadeIn();
            //         }
            //     }
            // });
        });


    }).apply(this, [jQuery]);
</script>
<?= $this->endSection() ?>