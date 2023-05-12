<section class="section">
    <div class="section-header">

        <h1><?= $title; ?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
        </div>

    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-md-6">
                <div class="alert alert-primary" role="alert">
                    <h4 class="alert-heading">Setting Nozle dan Suhu</h4>
                    <hr>
                    <p class="mb-0">ON / OFF Nozle</p>
                    <p class="mb-0">Kondisi suhu hanya diisi angka!</p>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form action="<?= base_url('setting/change_setting'); ?>" method="POST">
                            <input type="hidden" value="<?= $data->id; ?>" name="id">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Status Pompa</label>
                                    <select class="form-control" name="status">
                                        <option value="0" <?php if ($data->status == 0) echo 'selected="selected"'; ?>>OFF</option>
                                        <option value="1" <?php if ($data->status == 1) echo 'selected="selected"'; ?>>ON</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Kondisi Suhu</label>
                                    <input type="text" class="form-control" name="kondisi_suhu" value="<?= $data->kondisi_suhu; ?>" placeholder="30">
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>