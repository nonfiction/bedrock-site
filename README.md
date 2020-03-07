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

## Backend

<https://github.com/nonfiction/bedrock>

<https://hub.docker.com/repository/docker/nonfiction/bedrock/>

- **bedrock:web** Wordpress on Apache (composer, wpcli)
- **bedrock:dev** Webpack Dev Server (npm)
- **bedrock:env** MySQL Client and .env generator (ruby, thor)
