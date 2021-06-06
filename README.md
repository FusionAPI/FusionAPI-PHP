[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]

<p align="center">
  <a href="https://fusionapi.dev/">
    <img src="https://i.imgur.com/oeEET0y.png" alt="Logo" width="80" height="80">
  </a>

  <h3 align="center">FusionAPI C# Package</h3>

  <p align="center">
    Utilize state of the art cloud authentication today to mitigate cyber attacks and provide your users a better experience with FusionAPI.
    <br />
    <a href="https://docs.fusionapi.dev/"><strong>Read the docs! »</strong></a>
    <br />
    <br />
    <a href="https://discord.gg/API">Join our Discord!</a>
    ·
    <a href="https://fusionapi.dev/">Get free authentication!</a>
  </p>
</p>

## Modules

1. Check42FA("username"); | **Checks if the user has 2FA enabled.**
2. Login("username", "password", "2fa optional"); | **Logs the user in.**
3. Register("username", "password", "token"); | **Creates the user a account.**
4. ResetPassword("old password", "new password"); | **Updates users password**
5. ValidateSession(); | **Validates if the users session is still active.**
6. GetUserVar("name"); | **Gets a var from the logged in users profile.**
7. SetUserVar("name", "value"); | **Sets a var into a users profile.**
8. GetAppVar("name"); | **Gets a var from your config page on fusion.**
9. ExecuteAPI("api id", "data"); | **Non authenticated API execution**
10. ExecuteFullAPI("api id", "data", "time"); | **Authenticated API execution**
11. ExecuteTimeAPI("api id", "data", "time"); | **Authenticated API execution**
12. ExecuteAuthAPI("api id", "data"); | **Authenticated API execution**
13. GetChat(); | **Gets the chat blob from your application.**
14. DeleteMessage("message id"); | **Delete message from app.**
15. EditMessage("message id", "new message"); | **Edit message in app.**
16. SendMessage("message"); | **Send message in app.**


## Contributing

Contributions are what make the open source community such an amazing place to be learn, inspire, and create. Any contributions you make are **greatly appreciated**.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request
