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
    }
});

let addComicForm = document.getElementById("addComicForm");

addComicForm.addEventListener("submit", function (event) {
    event.preventDefault();

    let comicCoverImage = document.getElementById("comicCoverImage");
    let comicFormData = getComicFormData();

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
                comicFormData.append('coverImageId', data.id);
                postComic(comicFormData);
            },
            error: function (data) {
                alert('Error. Could not create comic.');
                console.log(data);
            }
        });
    } else {
        postComic(comicFormData);
    }
});

function getComicFormData() {
    let comicFormData = new FormData(addComicForm);
    comicFormData.delete('comicCoverImage');

    return comicFormData;
}

function postComic(comicFormData) {
    $.ajax({
        type: 'POST',
        url: '/api/comics',
        cache: false,
        contentType: false,
        processData: false,
        data: comicFormData,
        success: function (data) {
            window.location = '/collection/overview/' + data.id;
        },
        error: function (data) {
            alert('Error. Could not create comic.');
            console.log(data);
        }
    });
}