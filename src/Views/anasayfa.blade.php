@extends('acr_file.index')
@section('acr_file')
    <!-- Main content -->
    <section class="content">
        <div class="row">
        <?php echo $file->menu($tab);
        $mesajlar = $file_model->mesajlar($tab, 0);
        ?>

        <!-- /.col -->
            <div class="col-md-9">
                <?php echo $msg; ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"></h3>
                        <div class="box-tools pull-right">
                            <div class="has-feedback">
                                <input type="text" class="form-control input-sm" placeholder="Mesajlarda Ara">
                                <span class="glyphicon glyphicon-search form-control-feedback"></span>
                            </div>
                        </div>
                        <!-- /.box-tools -->
                    </div>
                    <div style="clear:both;"></div>
                    <br>
                    <!-- /.box-header -->
                    <div class="box-body no-padding">
                        <form class="kutu_form">
                            <div class="mailbox-controls">
                                <!-- Check all button -->
                                <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                                </button>
                                <div class="btn-group">
                                    <button type="button" onclick="sec_sil()" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
                                </div>
                                <!-- /.btn-group -->
                                <button onclick="yenile()" type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                                <!-- /.pull-right -->
                            </div>
                            <div class="table-responsive mailbox-messages">
                                <table class="table table-hover table-striped">
                                    <tbody>

                                    <?php

                                    foreach ($mesajlar as $item) {
                                        echo $file->file_satir($item, $tab);
                                    }
                                    ?>
                                    </tbody>
                                </table>
                                <!-- /.table -->
                            </div>
                            <!-- /.mail-box-messages -->
                            <input type="hidden" id="tab" name="tab" value="<?php echo $tab ?>"/>
                        </form>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer no-padding">
                        <div class="mailbox-controls">
                            <!-- Check all button -->
                            <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                            </button>
                            <div class="btn-group">
                                <button onclick="sec_sil()" type="button" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>

                            </div>
                            <!-- /.btn-group -->
                            <button onclick="yenile()" type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                            <div class="pull-right">
                            <?php echo $mesajlar->appends(['tab' => $tab])->render(); ?>
                            <!-- /.btn-group -->
                            </div>
                            <!-- /.pull-right -->
                        </div>
                    </div>
                </div>
                <!-- /. box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <script>
        function sec_sil() {
            if (confirm('Seçilen öğeleri silmek istediğinizden emin misiniz?') == true) {
                $.ajax({
                    type   : 'post',
                    url    : '/acr/file/file_sec_sil',
                    data   : $(".kutu_form").serialize(),
                    success: function (veri) {
                        $.each(veri, function (key, val) {
                            $('#file_satir_' + val).hide();
                        })

                    }
                })
            }
        }
        function yenile() {
            location.reload();
        }


    </script>
@stop