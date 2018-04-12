
# Mautic InstantMailer
A Mautic plugin that enables sending template emails instantaneously.

## Introduction

Mautic only lets you modify the delivery method for all the emails. This plugin allows you to send template emails instantaneously without affecting your queue.

### Installation

You can clone the repository directly inside the plugins folder or place it there manually. We reccomend the first option, since it makes the update process easier.

#### Git

Clone this repo inside the **plugins** folder with the following command:
```sh
$ git clone https://github.com/hostnetbr/mautic-instantmailer.git MauticMailerBundle
```
Clear the cache running this command from the Mautic main folder.
```sh
$ php app/console cache:clear
```
Access the plugins page in the Mautic panel and click on **Install/Update Plugins**.

#### Manual

Download this project as a zip file and extract it to a folder named **MauticMailerBundle**.

Copy the folder to the **plugins** folder.

Clear the cache and install the plugin as described above in the **Git** Method.

### Activation

Access the plugins page and select the Mailer Configuration plugin.

Activate both options and apply changes.

It's all set. Enjoy.


### Update

Depending on how you installed, the update process is different.

#### Git

Access the plugin main folder and run the following command
```sh
$ git pull
```
Clear the cache and update the plugin in the administration

#### Manual

Redo the installation.

## Author
*  **Henrique Rodrigues** - *henrique@hostnet.com.br*
