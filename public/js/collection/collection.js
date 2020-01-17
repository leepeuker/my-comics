$('document').ready(function () {
    loadModalPublisherOptions();
    initRatingStars();

    document.getElementById('deleteButton').addEventListener('click', function (event) {
        $.ajax({
            type: 'DELETE',
            url: '/api/comics/' + document.getElementById('comicDetailId').value,
            success: function (data, textStatus) {
                if (textStatus === 'nocontent') {
                    window.location = '/collection/overview';
                }
            },
            error: function (data) {
                alert('Error. Could not delete comic.');
                console.log(data);
            }
        })
    });

    document.getElementById('addComicManuallyButton').addEventListener('click', function (event) {
        let comicCoverImage = document.getElementById('comicDetailCoverImage');

        if (comicCoverImage.files.length > 0) {
            let imageFormData = new FormData();
            imageFormData.append('image', comicCoverImage.files[0]);

            $.ajax({
                type: 'POST',
                url: '/api/images',
                cache: false,
                contentType: false,
                processData: false,
                data: imageFormData,
                success: function (data) {
                    document.getElementById('comicDetailCoverId').value = data.id;

                    $.ajax({
                        type: 'GET',
                        url: '/api/images',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: imageFormData,
                        success: function (data) {
                            document.getElementById('comicDetailCoverId').value = data.id;

                            postComic();
                        },
                        error: function (data) {
                            alert('Error. Could not load created image.');
                            console.log(data);
                        }
                    });
                    postComic();
                },
                error: function (data) {
                    alert('Error. Could not create image.');
                    console.log(data);
                }
            });
        } else {
            postComic();
        }
    });

    document.getElementById('addComicVineIdButton').addEventListener('click', function (event) {
        let comicVineId = document.getElementById('addComicVineIdFormInput').value;
        if (comicVineId === '') {
            document.getElementById('addComicVineIdAlertDiv').innerHTML +=
                '<div class="alert alert-danger alert-dismissible fade show" id="addComicVineIdAlert" role="alert">' +
                '   <button aria-label="Close" class="close" data-dismiss="alert" type="button">' +
                '       <span aria-hidden="true">&times;</span>' +
                '   </button>' +
                '   <span id="addComicVineIdAlertText">No ComicVineId was entered!</span>' +
                '</div>';
            return;
        }

        let matches = comicVineId.match(/^(4000-)?(\d+$)/);
        if (matches === null || matches[2] === null) {
            document.getElementById('addComicVineIdAlertDiv').innerHTML +=
                '<div class="alert alert-danger alert-dismissible fade show" id="addComicVineIdAlert" role="alert">' +
                '   <button aria-label="Close" class="close" data-dismiss="alert" type="button">' +
                '       <span aria-hidden="true">&times;</span>' +
                '   </button>' +
                '   <span id="addComicVineIdAlertText">Invalid ComicVineId format!</span>' +
                '</div>';
            return;
        }


        $.ajax({
            type: 'POST',
            url: '/api/comic/comic_vine_id',
            data: {
                'comicVineId': matches[2]
            },
            success: function (data) {
                document.getElementById('addComicVineIdAlertDiv').innerHTML +=
                    '<div class="alert alert-success alert-dismissible fade show" id="addComicVineIdAlert" role="alert">' +
                    '   <button aria-label="Close" class="close" data-dismiss="alert" type="button">' +
                    '       <span aria-hidden="true">&times;</span>' +
                    '   </button>' +
                    '   <span id="addComicVineIdAlertText">Created: ' + data.name + '</span>' +
                    '</div>';
            }
            ,
            error: function (data) {
                console.log(data);

                let errorMessage = 'Error: Status code ' + data.status
                if (data.responseJSON === null) {
                    errorMessage = data.responseJSON
                }

                document.getElementById('addComicVineIdAlertDiv').innerHTML +=
                    '<div class="alert alert-danger alert-dismissible fade show" id="addComicVineIdAlert" role="alert">' +
                    '   <button aria-label="Close" class="close" data-dismiss="alert" type="button">' +
                    '       <span aria-hidden="true">&times;</span>\n' +
                    '   </button>' +
                    '   <span id="addComicVineIdAlertText">' + errorMessage + '</span>' +
                    '</div>';
                console.log(document.getElementById('addComicVineIdAlertDiv').innerHTML)
            }
        })
    });

    document.getElementById('toggleAddModeButton').addEventListener('click', function (event) {
        if (document.getElementById('addComicVineIdForm').style.display === 'none') {
            setComicDetailFormStatus('addComicVineId')
        } else {
            setComicDetailFormStatus('addComicManually')
        }
    });

    document.getElementById('saveButton').addEventListener('click', function (event) {
        let comicCoverImage = document.getElementById('comicDetailCoverImage');

        if (comicCoverImage.files.length > 0) {
            let imageFormData = new FormData();
            imageFormData.append('image', comicCoverImage.files[0]);

            $.ajax({
                type: 'POST',
                url: '/api/images',
                cache: false,
                contentType: false,
                processData: false,
                data: imageFormData,
                success: function (data) {
                    document.getElementById('comicDetailCoverId').value = data.id;

                    $.ajax({
                        type: 'GET',
                        url: '/api/images',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: imageFormData,
                        success: function (data) {
                            document.getElementById('comicDetailCoverId').value = data.id;

                            putComic();
                        },
                        error: function (data) {
                            alert('Error. Could not create image.');
                            console.log(data);
                        }
                    });
                    putComic();
                },
                error: function (data) {
                    alert('Error. Could not create image.');
                    console.log(data);
                }
            });
        } else {
            putComic();
        }
    });

    document.getElementById('addButton').addEventListener('click', function (event) {
        setComicDetailFormStatus('addComicVineId')

        $('#addModal').modal('toggle')
    });


});

function setComicDetailFormStatus(status) {
    resetDetailModal();

    if (status === 'edit') {
        document.getElementById('comicDetailForm').style.display = 'block';
        document.getElementById('deleteButton').style.display = 'block';
        document.getElementById('saveButton').style.display = 'block';
        return;
    }
    if (status === 'addComicVineId') {
        document.getElementById('addComicVineIdForm').style.display = 'block';
        document.getElementById('comicDetailForm').classList.add('text-center');
        document.getElementById('toggleAddModeButton').style.display = 'block';
        document.getElementById('toggleAddModeButton').innerHTML = 'Add manually';
        document.getElementById('addComicVineIdButton').style.display = 'block';
        return;
    }
    if (status === 'addComicManually') {
        document.getElementById('comicDetailForm').style.display = 'block';
        document.getElementById('toggleAddModeButton').style.display = 'block';
        document.getElementById('toggleAddModeButton').innerHTML = 'Add automatically';
        document.getElementById('addComicManuallyButton').style.display = 'block';
        return;
    }

    alert('Unknown comic detail modal status: ' + status)
}

function initRatingStars() {
    $(".my-rating").starRating({
        useFullStars: true,
        emptyColor: 'lightgray',
        hoverColor: '#92b4f2',
        activeColor: '#3676e8',
        initialRating: 0,
        starSize: 35,
        totalStars: 3,
        disableAfterRate: false,
        useGradient: false,
        callback: function (currentRating, $el) {
            $("#rating").val(currentRating);
            document.getElementById('comicDetailRating').value = currentRating;
        }
    });
}

function loadModalPublisherOptions() {
    let select = document.getElementById('comicDetailPublisher');
    select.innerHTML = '';
    $.ajax({
        type: 'GET',
        url: '/api/publishers',
        success: function (data) {
            data.forEach(function (publisher) {
                let opt = document.createElement('option');
                opt.appendChild(document.createTextNode(publisher.name));
                opt.value = publisher.id;
                select.appendChild(opt);
            });
        },
        error: function (data) {
            alert('Could not load publishers.');
            console.log(data);
        }
    });
}

function reloadRatingStars() {
    $(".my-rating").starRating('setRating', document.getElementById('comicDetailRating').value)
}

function resetDetailModal() {
    document.getElementById('toggleAddModeButton').style.display = 'none';
    document.getElementById('deleteButton').style.display = 'none';
    document.getElementById('addComicVineIdButton').style.display = 'none';
    document.getElementById('addComicManuallyButton').style.display = 'none';
    document.getElementById('saveButton').style.display = 'none';

    document.getElementById('addComicVineIdForm').style.display = 'none';
    document.getElementById('comicDetailForm').style.display = 'none';
    document.getElementById('comicDetailForm').classList.remove('text-center');

    document.getElementById('comicDetailsModalCenterTitle').innerHTML = null;

    document.getElementById('comicDetailId').value = null;
    document.getElementById('comicDetailCoverId').value = null;
    document.getElementById('comicDetailName').value = null;
    document.getElementById('comicDetailPublisher').value = null;
    document.getElementById('comicDetailYear').value = null;
    document.getElementById('comicDetailDescription').value = null;
    document.getElementById('comicDetailAddedToCollection').value = null;
    document.getElementById('comicDetailPrice').value = null;
    document.getElementById('comicDetailComicVineId').value = null;
    document.getElementById('comicDetailRating').value = 0;

    reloadRatingStars();
}

$('#addModal').on('hidden.bs.modal', function (e) {
    resetDetailModal()
})

function showDetailModal(id) {
    $.ajax({
            type: 'GET',
            url: '/api/comics/' + id,
            success: function (data) {
                resetDetailModal();
                setComicDetailFormStatus('edit');

                document.getElementById('comicDetailsModalCenterTitle').innerHTML = data.name;

                document.getElementById('comicDetailId').value = data.id;
                document.getElementById('comicDetailCoverId').value = data.coverId;
                document.getElementById('comicDetailName').value = data.name;
                document.getElementById('comicDetailYear').value = data.year;
                document.getElementById('comicDetailPublisher').value = data.publisherId;
                document.getElementById('comicDetailDescription').value = data.description;
                document.getElementById('comicDetailAddedToCollection').value = data.addedToCollection === null ? '' : data.addedToCollection.substr(0, 10);
                document.getElementById('comicDetailPrice').value = data.price === null ? '' : data.price / 100;
                document.getElementById('comicDetailComicVineId').value = data.comicVineId;
                document.getElementById('comicDetailRating').value = data.rating === null ? 0 : data.rating;

                document.getElementById('saveButton').style.display = 'block';

                reloadRatingStars()
            },
            error: function (data) {
                console.log(data);
            }
        }
    );

    $('#addModal').modal('toggle')
}

function putComic() {
    $.ajax({
        type: 'PUT',
        url: '/api/comics/' + document.getElementById('comicDetailId').value,
        data: {
            'name': document.getElementById('comicDetailName').value,
            'coverId': document.getElementById('comicDetailCoverId').value,
            'comicVineId': document.getElementById('comicDetailComicVineId').value,
            'price': document.getElementById('comicDetailPrice').value * 100,
            'year': document.getElementById('comicDetailYear').value,
            'description': document.getElementById('comicDetailDescription').value,
            'publisherId': document.getElementById('comicDetailPublisher').value,
            'addedToCollection': document.getElementById('comicDetailAddedToCollection').value,
            'rating': document.getElementById('comicDetailRating').value,
        },
        success: function () {
            location.reload();
        },
        error: function (data) {
            alert('Error. Could not create comic.');
            console.log(data);
        }
    });
}

function postComic() {
    $.ajax({
        type: 'POST',
        url: '/api/comics',
        data: {
            'name': document.getElementById('comicDetailName').value,
            'coverId': document.getElementById('comicDetailCoverId').value,
            'comicVineId': document.getElementById('comicDetailComicVineId').value,
            'price': document.getElementById('comicDetailPrice').value * 100,
            'year': document.getElementById('comicDetailYear').value,
            'description': document.getElementById('comicDetailDescription').value,
            'publisherId': document.getElementById('comicDetailPublisher').value,
            'addedToCollection': document.getElementById('comicDetailAddedToCollection').value,
            'rating': document.getElementById('comicDetailRating').value,
        },
        success: function () {
            location.reload();
        },
        error: function (data) {
            alert('Error. Could not create comic.');
            console.log(data);
        }
    });
}