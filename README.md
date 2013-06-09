
# ci-proj-basis

A project template providing a better way to develop and deploy a website based on CodeIgniter framework.

## Features

* Use [CodeIgniter](http://ellislab.com/codeigniter) as a web framework.
	- Omit `index.php` in URL no matter where you place your website  
	(at document root, its subdirectory, or on a virtual host).
* Use [Git](http://git-scm.com/) as a version control system.
	- With pre-configured `.gitignore`.
	- Update CodeIgniter by updating the [system submodule](https://github.com/YiPo/ci-proj-system).
* Develop and test at local and then deploy to the live website.
	- The project is deploying-independent and easy to set up.
	- Deploy the website by `git push`.
* User-friendly configuration panel.
* The private zone.
* Compatible with Linux and Windows.

## Usage

### Create a New Repo

1. Download the [latest project template](https://github.com/YiPo/ci-proj-basis/archive/master.zip), and unzip it.

2. Rename the folder `ci-proj-basis-master` as your own project name.

3. Change directory to the folder, and execute the following commands:

	```
	git init
	git submodule add git://github.com/YiPo/ci-proj-system.git private/system
	git add .
	git commit -m "Initial commit"
	```

4. Push this local repo to your remote repo.  
(Replace `<your-remote-repo>` to where your remote repo is.)

	```
	git remote add origin <your-remote-repo>
	git push -u origin master
	```

### Deployment

For the concept, read the article [Using Git for Deployment](http://danbarber.me/using-git-for-deployment/).

###### If `remote` is on GitHub

Set the *webhook URL* to the page `ci-proj-admin/deploy.php` of your website, see [Post-Receive Hooks](https://help.github.com/articles/post-receive-hooks) for more information. You should set a password from the admin panel to restrict the accessing to the page. So your *webhook URL* may looks like this:

```
https://user:password@hostname.com/path/to/website/ci-proj-admin/deploy.php
```

###### If `remote` and `website` are on Different Hosts

Create the file `post-receive` in the `hooks` folder of the `remote` repo as follows:  
(Replace `user`, `password` and `path/to/website` to yours.)

```sh
#!/bin/sh
curl -k https://user:password@hostname.com/path/to/website/ci-proj-admin/deploy.php
```

and make sure the file is executable.

```
chmod +x post-receive
```

###### If `remote` and `website` are on the Same Host

Create the file `post-receive` in the `hooks` folder of the `remote` repo as follows:  
(Replace `<path-to-website>` to the path of the root folder of your website.)

```sh
#!/bin/sh
cd <path-to-website>
unset GIT_DIR
git pull
git submodule update --init --recursive
```

and make sure the file is executable.

```
chmod +x post-receive
```

### Clone a Repo

Run this command: (Replace `<your-remote-repo>` to where your remote repo is)

```
git clone --recursive <your-remote-repo>
```

or execute the following command after cloning.

```
git submodule update --init --recursive
```

### Configuration

The config files that may vary from deployments are extracted from the project. These files (as below) are stored as templates in the folder `ci-proj-admin/template`. Once you link to `ci-proj-admin/` and enter the *admin panel*, these files are copied to their corresponding place and ready for configuring.

- `/.htaccess` (`root/.htaccess`)
- `/index.php` (`root/index.php`)
- `ci-proj-admin/.htaccess` (`admin/.htaccess`)
- `private/application/config/database.php` (`config/database.php`)

### Private Zone

The `private` folder and anything in it are unreachable from the web.

## Thanks for

these libraries:

* [CodeIgniter](http://ellislab.com/codeigniter)
* [jQuery](http://jquery.com/)
* [Twitter Bootstrap](http://twitter.github.io/bootstrap/)

## License

This software is licensed under the terms of [the MIT License](https://github.com/YiPo/ci-proj-basis/blob/master/LICENSE.md).

