$(document).ready(function() {

var playButton = document.getElementById('play-button');
var audioPlayer = document.getElementById('audio-player');
var progressBar = document.getElementById('progress-bar');

playButton.addEventListener('click', togglePlay);
progressBar.addEventListener('input', updateProgress);

function togglePlay() {
  if (audioPlayer.paused) {
    audioPlayer.play();
    playButton.innerHTML = '<i class="fa-solid fa-pause fa-2xl"></i>';
  } else {
    audioPlayer.pause();
    playButton.innerHTML = '<i class="fa-solid fa-play fa-2xl"></i>';
  }
}

function updateProgress() {
  var progress = progressBar.value;
  var duration = audioPlayer.duration;
  var currentTime = (progress / 100) * duration;
  audioPlayer.currentTime = currentTime;
}

// Update progress bar as the audio plays
audioPlayer.addEventListener('timeupdate', function() {
  var currentTime = audioPlayer.currentTime;
  var duration = audioPlayer.duration;
  var progress = (currentTime / duration) * 100;
  progressBar.value = progress;
});

// Update play/pause button when audio playback ends
audioPlayer.addEventListener('ended', function() {
  playButton.innerHTML = '<i class="fa-solid fa-play fa-2xl"></i>';
});


});
