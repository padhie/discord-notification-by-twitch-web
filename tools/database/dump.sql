INSERT INTO settings (`key`, `value`) VALUES
    ('notificationActive', '1'),
    ('notificationInactiveUntil', '1970-01-01 00:00:00');

INSERT INTO users (`id`, `username`, `roles`, `password`) VALUES
    (1, 'admin', '["ROLE_ADMIN", "ROLE_USER"]', 'admin');

INSERT INTO notifications (`user_id`, `channel`, `web_hook`, `message`) VALUES
    (1, 'padhie', 'https://discordapp.com/api/webhooks/678981017251610655/z4iNMNSaQoRhg7wU5URTXyEgpiIAhRNP6ez3bw-GEN0_Q5y7Io677OYbo-Q5peqVZ3AU', 'Hey, <@&659105181514072074> ! WIR SIND LIVE! Kommt ran und seit mit dabei, wir warten schon auf euch!');