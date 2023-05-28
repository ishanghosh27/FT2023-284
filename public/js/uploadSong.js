// When album art of the song is clicked, music player appears, and starts playing
// the song.

$(document).ready(function() {
  $(".fSong").click(function(event) {
    event.preventDefault();
    var songTitle = $(this).find("#song-name").text();
    console.log("songTitle:", songTitle);
    var songArtist = $(this).find(".card-text-sm").text();
    console.log("songArtist:", songArtist);
    var songThumbnail = $(this).find(".card-img-top").attr("src");
    console.log("songThumbnail:", songThumbnail);
    var songMp3 = "/audio/" + songTitle + ".mp3";
    console.log("actual music", songMp3);

    $("#player-container").css("display", "block");
    $("#song-titled strong").text(songTitle);
    $("#artist").text(songArtist);
    $("#song-info img").attr("src", songThumbnail);
    $("#audio-player").attr("src", songMp3);
    $("#audio-player")[0].play();
    $("#play-button").html('<i class="fa-solid fa-pause fa-2xl"></i>');
  });
});

// When user clicks the 'x' button, music stops and the music player dissapears.
$(document).ready(function() {
  // Event listener for the "X" button
  $(".fa-xmark").click(function() {
    // Hide the player container
    $("#player-container").css("display", "none");
    // Stop the audio playback
    $("#audio-player")[0].pause();
    $("#play-button").html('<i class="fa-solid fa-play fa-2xl"></i>');
    $("#audio-player")[0].currentTime = 0;
  });
});


