# Hacker-news

## Code Review

- Instead of connecting to the database in every view you could do it once in header.php
- I see you have chunks of removed code in the comments, these parts can create confusion for others. Code that is not used should be removed and retrieved through git history.
- In update_post.php and update_user.php the inputs in the function createMessage is in Swedish while all other messages around the site is in English.
- You’re being inconsistent in how you’re naming your .php files. You are mixing camel case, kebab case and snake case. Choose one.
- Your file structure is easy to understand and I appreciate that you split your code into smaller files.
- When serving the folder with php server, the index page is under /views/ which is confusing. When serving the views folder with php server itself, you are no longer serving the stylings which is problematic, therefore extract the views folder into the root of the project.
- When using fonts make sure to use .woff and .woff2 instead of .ttf.
- Use the button tag instead of input in your forms when you submit to be more semantic.
- You have a lot of empty space in your files, mainly in index.php. Consider removing that for more compact code.
- Overall a great job!

## Added features by Gilda Eklöf

- As a user I'm able to delete my account along with all posts, upvotes and comments.
- As a user I'm able to upvote and remove upvote on comments.
