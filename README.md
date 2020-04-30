# 201Created Security #

This library provides functionality for management of users in applications where authentication and access control are required. It is implemented as a Symfony bundle for ease of use, but could also be used with other frameworks or no framework at all with some adjustments.

## Installation ##
 
- Add the following to composer.json:
 ```
 "repositories": [
     {
         "type": "vcs",
         "url": "git@bitbucket.org:c201/201created-security.git"
     }
 ]
```
- Run `composer require 201created-security` 
- Add the following to bundles.php:
```
Twig\Extra\TwigExtraBundle\TwigExtraBundle::class => ['all' => true],
C201\Security\C201SecurityBundle::class => ['all' => true],
```
- Run `php bin/console doctrine:migrations:diff` to create a Doctrine migration for the User entity. Check the migrations file and manually remove anything unrelated to the c201_users table. Execute the migration by running `php bin/console doctrine:migrations:migrate`
- Set the 'secret' configuration option, see below for details.
- The bundle uses Symfony Mailer to send email. If you wish to use any feature requiring email dispatch, create a mailer.yaml file in the config/packages folder with the following contents:
```
framework:
    mailer:
        dsn: '%env(MAILER_DSN)%'
 ```
- The above also requires a MAILER_DSN entry in .env, containing the email transport DSN, for example:
```
MAILER_URL=smtp://username:password@domain:port
```
- If you wish to use the password reset feature, see the reset_password configuration options below. 

## Configuration ##
 To change the values of configuration options from their defaults, create a c201_security.yaml file in the config/packages folder with the following contents:
 ```
c201_security:
    option_name: value
    another_option_name: value
 ```
 
### Available Options ###

### secret ###

- Type: string
- Default: none, value required

This will be used for hashing password reset tokens and other similar purposes. It is recommended to set this option to the APP_SECRET .env var.
 
#### reset_password.route ####
 
- Type: string
- Default: ''
 
Password reset functionality entails sending an email to the user, containing a link to a controller GET action handling the reset. To generate the link, the action must have a named route defined for it accepting a single parameter named 'token'. For example:
```
/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/forgot-password/{token}/", name="forgot_password", methods={"GET"})
     */
    public function forgotPasswordAction(Request $request, string $token): Response
    {
        // find a user based on the token, present a form to set password, etc
    }
}
```
The name of this route ('forgot_password' in the example above) must be set as the value of the configuration option.

If this option is not set, requesting a password reset will throw an exception.

#### reset_password.email_from ####
 
- Type: string
- Default: ''
 
Email address appearing in the From: field of reset password emails.
 
If this option is not set, requesting a password reset will throw an exception.
 
#### reset_password.email_subject ####
  
- Type: string
- Default: ''
  
Subject of reset password emails.
  
If this option is not set, requesting a password reset will throw an exception.
  
#### reset_password.request_expiration_minutes ####

- Type: integer
- Default: 60

Number of minutes for which password reset requests will be valid.

This is also available as a service container parameter under the same name.