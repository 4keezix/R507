# Configuration du déploiement FTP

## Secrets GitHub à configurer

Pour activer le déploiement automatique sur FTP, vous devez ajouter ces secrets dans votre repository GitHub :

### 1. Aller dans les paramètres GitHub

1. Aller sur votre repo : `https://github.com/VOTRE_USERNAME/R507`
2. Cliquer sur **Settings**
3. Dans le menu de gauche : **Secrets and variables** → **Actions**
4. Cliquer sur **New repository secret**

### 2. Ajouter les secrets

Créer ces 4 secrets :

| Nom du secret | Description | Exemple |
|---------------|-------------|---------|
| `FTP_SERVER` | Adresse du serveur FTP | `ftp.votresite.com` |
| `FTP_USERNAME` | Nom d'utilisateur FTP | `votre_user@votresite.com` |
| `FTP_PASSWORD` | Mot de passe FTP | `votre_mot_de_passe` |
| `FTP_SERVER_DIR` | Dossier de destination | `/public_html/` ou `/www/` |

### 3. Configuration du workflow

Le déploiement se déclenchera automatiquement :
- ✅ Quand vous pushez sur la branche `master` ou `main`
- ✅ Après que tous les tests soient passés
- ✅ Seulement si les tests réussissent

### 4. Fichiers exclus du déploiement

Ces fichiers/dossiers ne seront PAS envoyés sur le FTP :
- `.git/` - Historique Git
- `node_modules/` - Dépendances Node
- `var/cache/` - Cache Symfony
- `var/log/` - Logs
- `.env.local` - Config locale
- `.env.*.local` - Configs locales

### 5. Configuration sur le serveur FTP

Une fois déployé, vous devez configurer sur votre serveur :

#### Créer le fichier `.env.local`

```bash
# Sur le serveur FTP, créer /public_html/.env.local
APP_ENV=prod
APP_SECRET=VOTRE_SECRET_PRODUCTION
DATABASE_URL="mysql://user:password@localhost:3306/database_prod"
```

#### Installer les dépendances

```bash
# Si vous avez accès SSH
composer install --no-dev --optimize-autoloader
php bin/console cache:clear
php bin/console cache:warmup
```

#### Créer la base de données

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction
```

### 6. Tester le déploiement

Pour tester :

```bash
# Push sur master
git checkout master
git merge develop
git push origin master
```

Le workflow va :
1. ✅ Exécuter les tests
2. ✅ Si OK : déployer sur FTP
3. ✅ Vous notifier du résultat

### 7. Voir les logs de déploiement

1. Aller sur GitHub → Actions
2. Cliquer sur le workflow en cours
3. Voir le job "Deploy to FTP"
4. Consulter les logs

## Commandes utiles

### Déploiement manuel (si besoin)

Si vous voulez déployer sans passer par le CI :

```bash
# Avec lftp
lftp -u username,password ftp.votresite.com << EOF
mirror -R --exclude .git/ --exclude var/cache/ --exclude var/log/ ./ /public_html/
bye
EOF
```

### Vérifier que le déploiement fonctionne

Après déploiement, tester l'application :
```
https://votresite.com
```

## Troubleshooting

### Le déploiement échoue

Vérifier :
- ✅ Les secrets GitHub sont bien configurés
- ✅ Le serveur FTP est accessible
- ✅ Le dossier de destination existe
- ✅ Les droits d'écriture sont OK

### L'application ne fonctionne pas après déploiement

Vérifier sur le serveur :
- ✅ `.env.local` existe avec les bonnes variables
- ✅ `vendor/` est présent (sinon : `composer install`)
- ✅ Les permissions sont correctes (755 pour dossiers, 644 pour fichiers)
- ✅ La base de données est créée et migrée

## Sécurité

⚠️ **Important** :
- Ne JAMAIS commiter les secrets
- Ne JAMAIS mettre les mots de passe dans le code
- Utiliser toujours les GitHub Secrets
- Changer les mots de passe régulièrement
