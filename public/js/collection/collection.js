$('document').ready(function () {
    loadModalPublisherOptions()
});

document.getElementById('deleteButton').addEventListener('click', function (event) {
    $.ajax({
        type: 'DELETE',
        url: '/api/comics/' + document.getElementById('comicId').value,
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

document.getElementById('saveButton').addEventListener('click', function (event) {
    let comicCoverImage = document.getElementById('comicCoverImage');

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
                document.getElementById('comicCoverId').value = data.id;

                $.ajax({
                    type: 'GET',
                    url: '/api/images',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: imageFormData,
                    success: function (data) {
                        document.getElementById('comicCoverId').value = data.id;

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

function loadModalPublisherOptions() {
    let select = document.getElementById('comicPublisher');
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
    $(".my-rating").starRating('setRating', document.getElementById('comicRating').value)
}

function resetDetailModal() {
    document.getElementById('comicDetailsModalCenterTitle').innerHTML = null;

    document.getElementById('comicId').value = null;
    document.getElementById('comicCoverId').value = null;
    document.getElementById('comicName').value = null;
    document.getElementById('comicPublisher').value = null;
    document.getElementById('comicYear').value = null;
    document.getElementById('comicDescription').value = null;
    document.getElementById('comicAddedToCollection').value = null
    document.getElementById('comicPrice').value = null;
    document.getElementById('comicComicVineId').value = null;
    document.getElementById('comicRating').value = null;
}

function showDetailModal(id) {
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
            document.getElementById('comicRating').value = currentRating;
        }
    });

    $.ajax({
            type: 'GET',
            url: '/api/comics/' + id,
            success: function (data) {
                resetDetailModal();

                document.getElementById('comicDetailsModalCenterTitle').innerHTML = data.name;

                document.getElementById('comicId').value = data.id;
                document.getElementById('comicCoverId').value = data.coverId;
                document.getElementById('comicName').value = data.name;
                document.getElementById('comicYear').value = data.year;
                document.getElementById('comicPublisher').value = data.publisherId
                document.getElementById('comicDescription').value = data.description;
                document.getElementById('comicAddedToCollection').value = data.addedToCollection.substr(0, 10);
                document.getElementById('comicPrice').value = data.price === null ? '' : data.price / 100;
                document.getElementById('comicComicVineId').value = data.comicVineId;
                document.getElementById('comicRating').value = data.rating === null ? 0 : data.rating;
                reloadRatingStars()
            },
            error: function (data) {
                console.log(data);
            }
        }
    );

    $('#comicDetailsModal').modal('toggle')
}

function putComic() {
    $.ajax({
        type: 'PUT',
        url: '/api/comics/' + document.getElementById('comicId').value,
        data: {
            'name': document.getElementById('comicName').value,
            'coverId': document.getElementById('comicCoverId').value,
            'comicVineId': document.getElementById('comicComicVineId').value,
            'price': document.getElementById('comicPrice').value * 100,
            'year': document.getElementById('comicYear').value,
            'description': document.getElementById('comicDescription').value,
            'publisherId': document.getElementById('comicPublisher').value,
            'addedToCollection': document.getElementById('comicAddedToCollection').value,
            'rating': document.getElementById('comicRating').value,
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