# wp-content

Basically a personal kickstart package I built for Vagrant.

It will simply be initiated with the following command:

vv create -b kickstart -gr https://github.com/clstrfcuk/wp-content/

(Note: "-b kickstart" stands for a specific blueprint I made for Vagrant, which adds additional and up to date plugins from their sources.)

This repo is then added to the fresh WordPress installation Vagrant builds. Then I have all my standard plugins and themes I usually work with integrated in a new Vagrant environment.

(Note: Some of the themes and plugins in this package need a license, if you want to use them)

For this setup I use:

- Vagrant
- [Varying Vagrant Vagrants]: https://github.com/Varying-Vagrant-Vagrants/VVV
