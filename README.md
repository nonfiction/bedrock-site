# bedrock-site

A foundation for setting up new Wordpress sites, highly influenced by
<https://github.com/roots/bedrock>

## Installation

1. Generate new site from this template: <https://github.com/nonfiction/bedrock-site/generate>
2. Visit generated repo, for example: <https://github.com/nonfiction/hello-world>
3. Clone repo, enter directory and install: 

```
git clone git@github.com:nonfiction/hello-world.git
cd hello-world
make install
make up
```

## Development

Common commands are available via Makefile. For example:

### Add a WordPress plugin or theme from wpackagist.org

```
make plugin add=wordpress-seo
make theme add=hueman
```

### Add an NPM package from npmjs.com

```
make package add=normalize.css
```

### Compile Assets with Webpack

```
make assets
```

### Launch in development mode with HMR

```
make up
```

### Launch in production mode with compiled assets

```
make upp
```

### Choose deploy host to target

```
make target
```

### Deploy

```
make deploy
```

Note: composer installation happens during Docker build, so any
new WordPress plugins or themes require a fresh Docker build.

## Backend

<https://github.com/nonfiction/bedrock>  
<https://hub.docker.com/repository/docker/nonfiction/bedrock/>

- **bedrock:web** Wordpress on Apache (composer, wpcli)
- **bedrock:dev** Webpack Dev Server (npm)
- **bedrock:env** MySQL Client and .env generator (ruby, thor)
