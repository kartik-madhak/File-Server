let selectedEntity = null
let folders, files
let app = $('#app');

$(function () {
    $.post('/folder/0', function (data, status) {
        data = JSON.parse(data)
        folders = data['folders']
        files = data['files']
        for (let i = 0; i < folders.length; ++i) {
            app.append(displayFolder(folders[i]['name']))
        }
        for (let i = 0; i < files.length; ++i)
            app.append(displayFile(files[i]['name']))
    });
})

function clickEntity(entity) {
    if(selectedEntity !== entity)
        $(selectedEntity).removeClass('blue-background');
    // console.log(selectedEntity)
    selectedEntity = entity;
    $(entity).toggleClass('blue-background');
}

function displayFolder(name) {
    return '<div class="text-center pl-3 pr-3 m-1" onclick="clickEntity(this)">' +
        '<span class="fas fa-folder d-block" style="font-size: 4rem"></span>' +
        '<div> ' +
        name +
        '</div>' +
        '</div>'
}

function displayFile(name) {
    return '<div class="text-center pl-3 pr-3 m-1" onclick="clickEntity(this)">' +
        '<span class="fas fa-file d-block" style="font-size: 4rem"></span>' +
        '<div> ' +
        name +
        '</div>' +
        '</div>'
}

function addFolder() {
    const foldername = $('#folderAdd_folderName').val()
    if (foldername.length === 0) {
        $('#folderAdd_folderName').addClass('border-danger');
        $('#folderAdd_folderName_label')[0].innerText = 'Folder name cannot be empty';
        $('#folderAdd_folderName_label')[0].style.color = 'red';
    } else {
        if (
            folders.find
            (
                function (folder) {
                    return (folder['name'] === foldername)
                }
            )) {
            $('#folderAdd_folderName').addClass('border-danger');
            $('#folderAdd_folderName_label').innerText = 'Folder name must be unique inside each directory';
            $('#folderAdd_folderName_label').style.color = 'red';
        } else {
            $('#myModal').modal('hide')

            $.post('/folder-add', {'folder_name': foldername}, function (data, status) {
                data = JSON.parse(data)
                console.log(data)
                app.append(displayFolder(foldername))
            });
        }
    }
}