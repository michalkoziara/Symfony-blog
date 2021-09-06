# Symfony Blog
### Web page for publishing articles

The goal of this project is to create a web page that allows users to post and manage their articles.

## Getting Started

These instructions will get you a copy of the project up and running on 
your local machine for development and testing purposes.

### Prerequisites

* PHP [7.1.3+] - https://www.php.net/
* Composer - https://getcomposer.org/
* APCu [on Windows] - [Detailed Instructions](https://stackoverflow.com/questions/24448261/how-to-install-apcu-in-windows)

Detailed information about installation and configurations are provided at developers' site.

For dockerization of this project see my [Symfony-blog-docker](https://github.com/michalkoziara/Symfony-blog-docker) repository.

## Technology Stack

* Symfony [4.2]
* PHP [7]
* MySQL for local development and testing
* Heroku + PostgreSQL for the production environment

### Setup 

A step by step instruction [on Windows]:
* Navigate to project directory.
* Run the following commands:

    ``composer install``
    
    ``php bin\console server:run``

The application should be ready to build and run locally now.

The default database is MySQL with the following address:
``mysql://root:@127.0.0.1:3306/app``

In order to change the database, ``.env`` and ``config/packages/doctrine.yaml`` files should be modified accordingly.

## Preview

<table>
    <tr>
        <td>
            <p>Homepage</p>
            <img src="images/img_1.png" alt="homepage" title="Homepage">
        </td>
        <td>
            <p>Article</p>
            <img src="images/img_2.png" alt="article" title="Article">
        </td>
    </tr>
    <tr>
        <td>
            <p>Article Administration Panel</p>
            <img src="images/img_3.png" alt="article administration panel" title="Article Administration Panel">
        </td>
        <td>
            <p>Creating Article</p>
            <img src="images/img_4.png" alt="creating article" title="Creating Article">
        </td>
    </tr>
    <tr>
        <td>
            <p>Slack Integration</p>
            <p>When an article is published, a message is sent to Slack.</p>
            <img src="images/img_5.png" alt="slack integration" title="Slack Integration">
        </td>
        <td>
            <p>Comment Administration Panel</p>
            <img src="images/img_6.png" alt="comment administration panel" title="Comment Administration Panel">
        </td>
    </tr>
</table>


## Author

* **Michał Koziara** 
