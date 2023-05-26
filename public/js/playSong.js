
function playSong(id) {
  event.preventDefault();
  $("#player-container").css("display", "block");
  $("#audio-player").attr("src", "../audio/DIL-NU.mp3");
  $("#audio-player")[0].play();
  // $.ajax({
  //   type: 'POST',
  //   url: '/playSong',
  //   data:
  //   {
  //       commentid: id
  //   },
  //   dataType: "text",
  //   success: function(response) {
  //       console.log(response);
  //   },
  //   error: function(error) {
  //       console.log(error);
  //   }
  // });
}


$(document).ready(function() {
  $(".fSong").click(function () {
    event.preventDefault();
    var a = $(this).children().children(".card-body").children().children().text();
    $("#player-container").css("display", "block");
    $("#audio-player").attr("src", "../audio/" + a + ".mp3");
    $("#audio-player")[0].play();
    $("#play-button").html('<i class="fa-solid fa-pause fa-2xl"></i>')
  });
});

// $(document).ready(function() {
//   $(".fSong").click(function (event) {
//       event.preventDefault();
//       var songTitle = $(this).find(".song-name").text();
//       var songArtist = $(this).find(".card-text-sm").text();
//       var songThumbnail = $(this).find(".card-img-top").attr("src");
//       var songMp3 = $(this).data("mp3-url");

//       $("#player-container").css("display", "block");
//       $("#song-titled strong").text(songTitle);
//       $("#artist").text(songArtist);
//       $("#song-info img").attr("src", songThumbnail);
//       $("#audio-player").attr("src", songMp3);
//       $("#audio-player")[0].play();
//       $("#play-button").html('<i class="fa-solid fa-pause fa-2xl"></i>');
//   });
// });

