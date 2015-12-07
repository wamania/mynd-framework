# Installation #

Cette installation fonctionne sous linux. Il n' a à priori aucune raison qu'elle ne fonctionne pas sous windows (mais il faudrait être fou pour développer du php sous windows...)

## Apache ##
Je considère que vous avez déjà un apache installé et que vous avez un virtualhost par défaut qui pointe sur /var/www

Il faudra ajouter à ce virtualhost
```
SetEnv environment local
```
au virtualhost par défaut pour utiliser la génération de projet de Mynd

Pensez aussi au
```
AllowOverride all
```
nécessaire pour la réécriture d'URL. Pensez aussi à activer le mod\_rewrite d'apache.

Voici un exemple allégé de fichier /etc/apache2/sites-available/default
```
<VirtualHost *:80>
	ServerAdmin webmaster@localhost
	DocumentRoot /var/www
	<Directory />
		Options FollowSymLinks
		AllowOverride None
	</Directory>
	<Directory /var/www>
		Options Indexes FollowSymLinks MultiViews
		AllowOverride All
		Order allow,deny
		allow from all
		SetEnv environment local
	</Directory>
</VirtualHost>
```

## Récupération de Mynd Framework ##
Comme indiquer sur la page de google code, il faut installer git, puis
```
cd /var/www
git clone https://wamania@code.google.com/p/mynd-framework/
```

## Génération du projet ##
Avec votre navigateur, aller sur http://localhost/mynd-framework
Vous pouvez laisser les options par défaut, sauf si vous savez ce que vous faites, indiquez un nom, par exemple nous allons créer un gestionnaire de contact, appelons le "contacts". Lancer la génération.

Rendez-vous maintenant sur http://localhost/contacts