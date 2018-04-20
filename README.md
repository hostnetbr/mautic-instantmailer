
# Mautic InstantMailer
A Mautic plugin that enables sending template emails instantaneously.

## Introduction

Mautic only lets you modify the delivery method for all the emails. This plugin allows you to send template emails instantaneously without affecting your queue.

### Installation

Download this project as a zip file and extract the content from the zip file.

Copy the **HostnetMailerBundle** folder to the **plugins** folder.

Clear the cache running this command from the Mautic main folder.
```sh
$ php app/console cache:clear
```
Access the plugins page in the Mautic panel and click on **Install/Update Plugins**.

### Activation

Access the plugins page and select the Mailer Configuration plugin.

Activate both options and apply changes.

It's all set. Enjoy.
## Author
*  **Henrique Rodrigues** - *henrique@hostnet.com.br*
