const params = new URLSearchParams(window.location.search);
const $statusSuccess = document.querySelector("#success");
const $statusError = document.querySelector("#error");
if (params.has("success")) {
  $statusSuccess.style.display = "block";
  $statusSuccess.innerText = params.get("success");
} else {
  $statusSuccess.style.display = "none";
}
if (params.has("error")) {
  $statusError.style.display = "block";
  $statusError.innerText = params.get("error");
} else {
  $statusError.style.display = "none";
}
