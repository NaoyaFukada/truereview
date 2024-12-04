# Overview

This project was developed as part of **Assignment 1** for the **2703ICT Web Application Development** course at Griffith University. The assignment's primary goal was to provide students with hands-on experience in building a Laravel-based web application while adhering to specific limitations. This project serves as the foundation for learning other frameworks and advanced Laravel features in subsequent assignments.

## Key Restrictions:

-   Advanced Laravel features such as **controllers, models, migrations/seeders, and validation** were prohibited.

-   Focus was on using Laravel's **routing and templating/view** features.

-   **Raw SQL** was required for database interactions.

# Development Reflection

I started this assignment by thoroughly reviewing my lecture notes to reinforce what I had learned and to fully understand the scope and requirements. This upfront understanding helped me map out a clear development plan, ensuring that I wouldn’t have to modify functionality I’d already built in the future. My initial focus was on writing SQL queries and setting up the database to have a solid foundation before working on displaying any data on the pages. Since page creation was more straightforward, I prioritized completing all the static pages, except the form, to get accustomed to Laravel’s structure. Once the basic pages were in place, I moved on to creating the form and implementing the fake review algorithm. During the process, I encountered common issues such as typos or logical errors while connecting the front end with the back end. However, Laravel’s detailed error messages and the use of dd() to debug the retrieved data made it easier to resolve bugs. I made it a point to test my code regularly after writing a few lines to prevent future issues from piling up.

# Fake Review Detection System

The system employs multiple criteria to identify potentially fake reviews:

1. Daily Review Limit: One user cannot submit more than five reviews per day.

2. Profanity Filter: I use a list of inappropriate words to detect offensive language in reviews.

3. Sensitive Information Detection: The system scans for phone numbers, emails, and URLs to avoid personal information being posted.

4. Word Bias Check: Reviews with over five positive or negative words are flagged to avoid excessive bias.

5. Length and Repetition Check: Reviews that are either too short or too long are not allowed, as well as too many repetitive words in a review.

If even one of the predefined criteria is not met, the system flags the review as potentially fake and notifies the user with an error message indicating the issue.

# Key Technologies Used

-   **Laravel**: PHP Framework used for routing and templating features.

-   **Bootstrap**: CSS Library used for responsive and intuitive UI design.

-   **SQlite**: Relational database management system that uses SQL to interact with the database.

-   **Font Awesome**: Used for icons.
