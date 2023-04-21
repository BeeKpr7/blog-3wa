let offset = 8;
let lock = false;

// Lock to prevent multiple requests
function loadPosts() {
  console.log("click");
  fetch("/api/post/" + offset)
    .then((res) => res.text())
    .then((data) => {
      document.getElementById("posts").innerHTML += data;
      lock = false;
    });
  offset += 6;
}
// Load more posts when click button
document.getElementById("btn-load-more").addEventListener("click", loadPosts);
// Load more posts when scroll to bottom
window.addEventListener("scroll", function () {
  if (
    window.innerHeight + window.scrollY >=
      document.body.offsetHeight - window.innerHeight / 3 &&
    !lock
  ) {
    lock = true;
    loadPosts();
  }
});
