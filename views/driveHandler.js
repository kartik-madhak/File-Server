class Entity {
    constructor(id, user_id, parent_folder_id, name, size, created_at, updated_at) {
        this.id = id
        this.user_id = user_id
        this.parent_folder_id = parent_folder_id
        this.name = name
        this.size = size
        this.created_at = created_at
        this.updated_at = updated_at
    }
}

class Folder extends Entity {
    constructor(id, user_id, parent_folder_id, name, size, no_of_items, created_at, updated_at) {
        super(id, user_id, parent_folder_id, name, size, created_at, updated_at)
        this.no_of_items = no_of_items
    }
}

class File extends Entity {
    constructor(id, user_id, parent_folder_id, name, size, path, created_at, updated_at) {
        super(id, user_id, parent_folder_id, name, size, created_at, updated_at)
        this.path = path
    }
}

class DriveHandler {
    constructor() {
        this.rootFolder = null
        this.selectedEntity = null
        this.folders = []
        this.files = []
        this.app = $('#app')
        this.loadFolder(0)

        let _thisRef = this
        this.app.click(function () {
            $(_thisRef.selectedEntity).removeClass("blue-highlight")
            _thisRef.selectedEntity = false
            $('#file_download_button').addClass('disabled')
            _thisRef.showInfo()
        })

        this.app.on('click', '.clickable_entity', function () {
            let entity = this

            // If different entity is clicked
            if (_thisRef.selectedEntity !== entity) {
                $(_thisRef.selectedEntity).removeClass("blue-highlight")
                _thisRef.selectedEntity = entity
                $(_thisRef.selectedEntity).addClass("blue-highlight")
            } else if (_thisRef.selectedEntity === entity) {

                let id = $(_thisRef.selectedEntity).attr('id')
                if (id.includes('folder')) {
                    _thisRef.loadFolder(id.split('_')[1])
                }
                $(_thisRef.selectedEntity).removeClass("blue-highlight")
                _thisRef.selectedEntity = null
            }

            // Download button enable / disable
            let downloadButton = $('#file_download_button')
            if (_thisRef.selectedEntity) {
                downloadButton.removeClass("disabled")
            } else {
                downloadButton.addClass("disabled")
            }

            _thisRef.showInfo()
            return false
        })

        $('#drive_handle_back').on('click', () => this.back())
        $('#file_download_button').on('click', () => this.download())
        $('#folder_add_button').on('click', () => this.addFolder())
        $('#files_upload_button').on('click', () => this.uploadFiles())
    }

    addEntitiesFromAPI(folders, files) {
        if (folders != null) {
            for (let i = 0; i < folders.length; ++i) {
                this.folders.push(Object.assign(new Folder, folders[i]))
            }
            this.folders.sort(function (a, b) {
                let keyA = new Date(a.updated_at),
                    keyB = new Date(b.updated_at);
                // Compare the 2 dates
                if (keyA < keyB) return 1;
                if (keyA > keyB) return -1;
                return 0;
            });
        }
        if (files != null) {
            for (let i = 0; i < files.length; ++i) {
                this.files.push(Object.assign(new File, files[i]))
            }
            this.files.sort(function (a, b) {
                let keyA = new Date(a.updated_at),
                    keyB = new Date(b.updated_at);
                if (keyA < keyB) return 1;
                if (keyA > keyB) return -1;
                return 0;
            });
        }

        //TODO: SORT folders and files and Redraw GUI

        this.display()
    }

    loadFolder(folderId) {
        this.app.innerHTML = ''
        let _thisRef = this
        $.post('/folder/' + folderId, function (data, status) {
            data = JSON.parse(data)
            _thisRef.rootFolder = Object.assign(new Folder, data.mainFolder)
            _thisRef.resetEntities()
            _thisRef.addEntitiesFromAPI(data.folders, data.files)
            _thisRef.showInfo()
        })
    }

    static #returnFolderDisplayString(folder, i) {
        return '<div class="text-center col-2 mt-4">' +
            '<span class="fas fa-folder d-block clickable_entity" data-index="' + i + '" id="folder_' + folder.id + '" style="font-size: 5rem"></span>' +
            '<div> ' +
            folder.name +
            '</div>' +
            '</div>'
    }

    static #fAIconBasedOnFormat(name) {
        let format = name.split('.').pop().toLowerCase()
        switch (format) {
            case 'pdf':
                return 'fa-file-pdf'
            case 'txt':
                return 'fa-file'
            case 'c':
            case 'cpp':
            case 'py':
            case 'java':
            case 'html':
            case 'css':
            case 'js':
            case 'php':
            case 'sql':
                return 'fa-file-code'
            case 'doc':
            case 'docx':
                return 'fa-file-word'
            case 'ppt':
            case 'pptx':
                return 'fa-file-powerpoint'
            case 'mp4':
            case 'm4v':
            case 'mpeg':
                return 'fa-file-video'
            case 'png':
            case 'jpg':
                return 'fa-file-image'
            case 'mp3':
            case 'm4a':
                return 'fa-file-audio'
            default:
                return 'fa-file'
        }

    }

    static #returnFileDisplayString(file, i) {
        return '<div class="text-center col-2 mt-4">' +
            '<span class="fas ' + DriveHandler.#fAIconBasedOnFormat(file.name) + ' d-block clickable_entity" data-index="' + i + '" id="file_' + file.id + '" style="font-size: 5rem"></span>' +
            '<div class="text-truncate"> ' +
            file.name +
            '</div>' +
            '</div>'
    }

    display() {
        this.app.empty()

        for (let i = 0; i < this.folders.length; ++i) {
            this.app.append(DriveHandler.#returnFolderDisplayString(this.folders[i], i))
        }
        for (let i = 0; i < this.files.length; ++i) {
            this.app.append(DriveHandler.#returnFileDisplayString(this.files[i], i))
        }

    }

    addFolder() {
        const folderInput = $('#folderAdd_folderName')
        const folderName = folderInput.val()
        const folderNameLabel = $('#folderAdd_folderName_label')[0];
        if (folderName.length === 0) {
            folderInput.addClass('border-danger')
            folderNameLabel.innerText = 'Folder name cannot be empty'
            folderNameLabel.style.color = 'red'
        } else {
            if (this.folders.find(function (folder) {
                return (folder.name === folderName)
            })) {
                folderInput.addClass('border-danger')
                folderNameLabel.innerText = 'Folder name must be unique inside each directory'
                folderNameLabel.style.color = 'red'
            } else {
                $('#addFolderModal').modal('hide')
                console.log(folderName)

                let _thisRef = this
                $.ajax({
                    url: '/folder-add',
                    type: 'POST',
                    dataType: 'json',
                    data: {'folder_name': folderName},
                    success: function (data, status) {
                        _thisRef.rootFolder = Object.assign(new Folder, data.mainFolder)
                        _thisRef.addEntitiesFromAPI([data.folder], null)
                        _thisRef.showInfo()
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText)
                    }
                })
            }
        }
    }

    uploadFiles() {
        let formData = new FormData()
        let files = document.getElementById("files_to_upload").files
        for (let i = 0; i < files.length; i++) {
            formData.append('file_' + i, files[i])
        }
        let _thisRef = this
        $.ajax({
            url: '/files-add',
            type: 'POST',
            dataType: 'json',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data, status) {
                console.log(data)
                if(data.msg !== 'FAILED') {
                    _thisRef.rootFolder = Object.assign(new Folder, data.mainFolder)
                    _thisRef.addEntitiesFromAPI(null, data.files)
                    _thisRef.showInfo()
                    // $(document.getElementById("files_to_upload")).val('')
                    $('#uploadFilesModal').modal('hide')
                } else {
                    $('#files_to_upload_label')[0].innerText = data.error
                    $('#files_to_upload_label')[0].classList.add('text-danger')
                }
            },
            error: function (data) {
                $('#files_to_upload_label')[0].innerText = data.msg;
            }
        })
    }

    showInfo() {
        let info = $('#entity-info')[0]
        let values = {}
        if (this.selectedEntity) {
            let index = $(this.selectedEntity).data('index')
            if ($(this.selectedEntity).attr('id').includes('folder')) {
                let folder = this.folders[index]

                values = {
                    'name': folder.name,
                    'size': (folder.size / (1024 * 1024)).toFixed(2) + ' MB',
                    'created': folder.created_at,
                    'updated': folder.updated_at,
                    'number of items inside': folder.no_of_items
                }
            } else {
                let file = this.files[index]

                values = {
                    'name': file.name,
                    'size': (file.size / (1024 * 1024)).toFixed(2) + ' MB',
                    'created': file.created_at,
                    'updated': file.updated_at,
                }
            }
        } else {
            let folder = this.rootFolder
            values = {
                'name': folder.name,
                'size': (folder.size / (1024 * 1024)).toFixed(2) + ' MB',
                'created': folder.created_at,
                'updated': folder.updated_at,
                'number of items inside': folder.no_of_items
            }
        }

        let res = ''
        for (const [key, value] of Object.entries(values)) {
            res += '                    <tr>\n' +
                '                        <td class="text-muted">' + key + '</td>\n' +
                '                        <td>' + value + '</td>\n' +
                '                    </tr>\n'
        }

        info.innerHTML = '<div class = "ml-2" style="font-family: Droid Sans,serif; font-size: xx-large">Info</div>\n' +
            '                <table class="table table-borderless">\n' +
            res +
            '                </table>'
    }

    resetEntities() {
        this.folders = []
        this.files = []
    }

    back() {
        this.loadFolder(this.rootFolder.parent_folder_id)
    }

    download() {
        let _thisRef = this
        let index = $(this.selectedEntity).data('index')
        if ($(this.selectedEntity).attr('id').includes('file')) {
            window.location = '/download/file/' + _thisRef.files[index].id
        } else {
            window.location = '/download/folder/' + _thisRef.folders[index].id
        }
    }
}

$(
    function () {
        new DriveHandler()
    }
)
