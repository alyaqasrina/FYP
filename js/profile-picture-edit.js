function onProfilePictureEdit() {
    var profilePicture = document.getElementById('profile_picture_preview');
    var fileInput = document.getElementById('profile_picture');
    var fileReader = new FileReader();

    fileInput.onchange = function() {
        fileReader.readAsDataURL(fileInput.files[0]);
    }

    fileReader.onload = function() {
        profilePicture.src = fileReader.result;
    }
}