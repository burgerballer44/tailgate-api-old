<?php

use Phinx\Migration\AbstractMigration;

class CreateOauthClientsTable extends AbstractMigration
{
    public function up()
    {
        $this->execute('
            CREATE TABLE oauth_clients (
                client_id             VARCHAR(80)   NOT NULL,
                client_secret         VARCHAR(80),
                redirect_uri          VARCHAR(2000),
                grant_types           VARCHAR(80),
                scope                 VARCHAR(4000),
                user_id               VARCHAR(80),
                PRIMARY KEY (client_id)
            );

            CREATE TABLE oauth_access_tokens (
                access_token         VARCHAR(40)    NOT NULL,
                client_id            VARCHAR(80)    NOT NULL,
                user_id              VARCHAR(80),
                expires              TIMESTAMP      NOT NULL,
                scope                VARCHAR(4000),
                PRIMARY KEY (access_token)
            );

            -- CREATE TABLE oauth_authorization_codes (
            --   authorization_code  VARCHAR(40)     NOT NULL,
            --   client_id           VARCHAR(80)     NOT NULL,
            --   user_id             VARCHAR(80),
            --   redirect_uri        VARCHAR(2000),
            --   expires             TIMESTAMP       NOT NULL,
            --   scope               VARCHAR(4000),
            --   id_token            VARCHAR(1000),
            --   PRIMARY KEY (authorization_code)
            -- );

            CREATE TABLE oauth_refresh_tokens (
                refresh_token       VARCHAR(40)     NOT NULL,
                client_id           VARCHAR(80)     NOT NULL,
                user_id             VARCHAR(80),
                expires             TIMESTAMP       NOT NULL,
                scope               VARCHAR(4000),
                PRIMARY KEY (refresh_token)
            );

            -- CREATE TABLE oauth_users (
            --   username            VARCHAR(80),
            --   password            VARCHAR(80),
            --   first_name          VARCHAR(80),
            --   last_name           VARCHAR(80),
            --   email               VARCHAR(80),
            --   email_verified      BOOLEAN,
            --   scope               VARCHAR(4000),
            --   PRIMARY KEY (username)
            -- );

            -- CREATE TABLE oauth_scopes (
            --   scope               VARCHAR(80)     NOT NULL,
            --   is_default          BOOLEAN,
            --   PRIMARY KEY (scope)
            -- );

            -- CREATE TABLE oauth_jwt (
            --   client_id           VARCHAR(80)     NOT NULL,
            --   subject             VARCHAR(80),
            --   public_key          VARCHAR(2000)   NOT NULL
            -- );

            -- CREATE TABLE oauth_jti (
            --   issuer              VARCHAR(80)   NOT NULL,
            --   subject             VARCHAR(80),
            --   audiance            VARCHAR(80),
            --   expires             TIMESTAMP     NOT NULL,
            --   jti                 VARCHAR(2000) NOT NULL
            -- );

            -- CREATE TABLE oauth_public_keys (
            --   client_id            VARCHAR(80),
            --   public_key           VARCHAR(2000),
            --   private_key          VARCHAR(2000),
            --   encryption_algorithm VARCHAR(100) DEFAULT "RS256"
            -- )
        ');
    }

    public function down()
    {
        $this->table('oauth_clients')->drop()->save();
        $this->table('oauth_access_tokens')->drop()->save();
        // $this->table('oauth_authorization_codes')->drop()->save();
        $this->table('oauth_refresh_tokens')->drop()->save();
        // $this->table('oauth_users')->drop()->save();
        // $this->table('oauth_scopes')->drop()->save();
        // $this->table('oauth_jwt')->drop()->save();
        // $this->table('oauth_jti')->drop()->save();
        // $this->table('oauth_public_keys')->drop()->save();
    }
}
