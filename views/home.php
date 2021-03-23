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
                <button class="btn btn-sm" id="drive_handle_back">
                    <span class="fas fa-angle-left fa-2x" >
                    </span>
                </button>
                <button class="btn btn-sm disabled" id="file_download_button">
                    <span class="fas fa-file-download fa-2x">
                    </span>
                </button>
                <button class="btn btn-sm" onclick="$('#uploadFilesModal').modal('show');">
                    <span class="fas fa-file-upload fa-2x"></span>
                </button>
                <button class="btn btn-sm" onclick="$('#addFolderModal').modal('show');">
                    <span class="fas fa-folder-plus fa-2x"></span>
                </button>
            </div>

            <!-- MAIN GUI -->
            <div class="border rounded-lg"
                 style="max-height: 80vh;min-height: 80vh; overflow-y: scroll; overflow-x: hidden;">
                <div id="app" class="row p-4"
                     style="font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif; font-size: 13px;">

                </div>
            </div>
        </div>
        <div class="col-md-3 mt-5">
            <div id="entity-info"
                 class="border"
                 style="max-height: 80vh;min-height: 80vh;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif; font-size: 13px;">
                <!-- INFO BAR -->

            </div>
        </div>
    </div>

    <!-- Add Folder Modal -->
    <div id="addFolderModal" class="modal fade" role="dialog">
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
                    <button type="button" class="btn btn-primary" id="folder_add_button">Add new folder</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Files Modal -->
    <div id="uploadFilesModal" class="modal fade" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Choose Files</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="files_to_upload" id="files_to_upload_label">Write a name for the
                        folder</label>
                    <input multiple="multiple" type="file" placeholder="Choose file(s)" class="form-control"
                           id="files_to_upload">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="files_upload_button">Upload Files</button>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .blue-highlight {
        color: black !important;
    }

    #app span {
        opacity: 0.8;
    }

</style>

<script src="views/driveHandler.js"></script>

</body>
</html>