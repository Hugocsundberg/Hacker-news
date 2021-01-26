"use strict";

const likeCommentForms = document.querySelectorAll(".like-comment-form");

likeCommentForms.forEach((likeForm) => {
  likeForm.addEventListener("submit", (event) => {
    event.preventDefault();

    const formData = new FormData(likeForm);

    fetch("/Account/like-comment.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        return response.json();
      })
      .then((json) => {
        const upvoteButton = event.target.querySelector(".like-comment-button");
        const upvoteStatus = json.status;

        if (upvoteStatus === "unvote") {
          upvoteButton.style.backgroundColor = "black";
        } else {
          upvoteButton.style.backgroundColor = "rgb(37, 104, 246)";
        }

        const voteCounts = document.querySelectorAll(".vote-number");
        voteCounts.forEach((voteCount) => {
          if (upvoteButton.dataset.id === voteCount.dataset.id) {
            voteCount.textContent = json.voteCount;
          }
        });
      });
  });
});
