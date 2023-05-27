
// function playSong(id) {
//   event.preventDefault();
//   $("#player-container").css("display", "block");
//   $("#audio-player").attr("src", "../audio/DIL-NU.mp3");
//   $("#audio-player")[0].play();
//   // $.ajax({
//   //   type: 'POST',
//   //   url: '/playSong',
//   //   data:
//   //   {
//   //       commentid: id
//   //   },
//   //   dataType: "text",
//   //   success: function(response) {
//   //       console.log(response);
//   //   },
//   //   error: function(error) {
//   //       console.log(error);
//   //   }
//   // });
// }


// $(document).ready(function() {
//   $(".fSong").click(function (event) { // Add the 'event' parameter here
//     event.preventDefault();

//     console.log(a);
//     $("#player-container").css("display", "block");
//     $("#audio-player").attr("src", "../public/audio/" + a + ".mp3");
//     $("#audio-player")[0].play();
//     $("#play-button").html('<i class="fa-solid fa-pause fa-2xl"></i>');
//   });
// });

$(document).ready(function() {
  $(".fSong").click(function(event) {
    event.preventDefault();
    debugger;
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


