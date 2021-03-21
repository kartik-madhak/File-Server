<!doctype html>
<html lang="en">
<?php include('layout/head.php') ?>
<body>
<?php include('layout/navbar.php') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div>
                <!-- SIDEBAR -->
            </div>
        </div>
        <div class="col-md">
            <div class="text-right mr-3">
                <!-- PUT CONTEXT ACTIONS HERE YOU DUMAS -->

                <button class="btn btn-sm" onclick="$('#myModal').modal('show');">
                    <span class="fas fa-folder-plus fa-2x"></span>
                </button>
            </div>

            <!-- MAIN GUI -->
            <div class="border rounded-lg" style="min-height: 80vh; overflow-y: scroll; overflow-x: hidden;">
                <div id="app" class="row p-4">

                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Folder</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="folderAdd_folderName" id="folderAdd_folderName_label">Write a name for the
                        folder</label>
                    <input type="text" class="form-control" minlength="1" id="folderAdd_folderName">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="addFolder()">Add new folder</button>
                </div>
            </div>
        </div>
    </div>


</div>

<style>
    .blue-background {
        background-color: rgba(0, 0, 255, 0.3);
    }

    span {
        opacity: 0.5 !important;
        color: blue;
    }
</style>

<script src="views/driveHandler.js"></script>

</body>
</html>