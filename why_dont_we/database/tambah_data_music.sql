-- ============================================================================
-- TAMBAHAN: isi tabel albums, songs, dan videos untuk halaman Music
-- Untuk database yang SUDAH dipakai (TinkerHost / XAMPP). Tabelnya sudah ada
-- (kosong); file ini hanya menambah baris, tidak menghapus apa pun.
-- Aman dijalankan lebih dari sekali (INSERT IGNORE melewati id yang sudah ada).
--
-- Cara: phpMyAdmin → pilih database → tab Import → pilih file ini → Go.
-- (Database baru cukup import why_dont_we.sql; data ini sudah ada di sana.)
-- ============================================================================

SET NAMES utf8mb4;

INSERT IGNORE INTO albums (id, title, type, release_year, cover_image) VALUES
  (1, 'Letters', 'album', 2018, 'assets/image/8letters.jpg'),
  (2, 'let me down easy (lie)', 'single', 2018, 'assets/image/flower.jpg'),
  (3, 'how do you love somebody', 'single', 2017, 'assets/image/HDYLS-new.jpg'),
  (4, 'just friends', 'single', 2019, 'assets/image/PRESAVE-COVER_1_0.jpg'),
  (5, 'good times and bad ones', 'album', 2017, 'assets/image/wdw-cover-final.jpg'),
  (6, 'love back', 'single', 2016, 'assets/image/lovebackcover1_0.jpg'),
  (7, 'Fallin', 'single', 2016, 'assets/image/fallin.jpg'),
  (8, 'these girl', 'single', 2019, 'assets/image/these-girl.png'),
  (9, 'trust fund baby', 'single', 2018, 'assets/image/trust-fund-baby.jpg'),
  (10, 'why don''t we just', 'single', 2020, 'assets/image/why-dont-we-just.jpg');

INSERT IGNORE INTO songs (id, album_id, title, duration_sec, audio_file, cover_image, lyrics, spotify_url, apple_url, youtube_url, is_featured) VALUES
  (1, NULL, 'fallin', NULL, 'assets/audio/fallin-adrenaline.mp3', 'https://t2.genius.com/unsafe/387x387/https%3A%2F%2Fimages.genius.com%2Ff064a6c8ae2d60a424f1c2a07533c3ff.1000x1000x1.png', '<p class="verse">
Caught in the moment, not even thinkin'' twice<br>
Everything''s frozen, nothing but you and I<br>
Can''t stop my heart from beating<br>
Why do I love this feeling?<br>
Make me a promise, tell me you''ll stay with me<br>
If I''m bein'' honest, I don''t know where this leads<br>
But that''s the only question<br>
Baby, don''t keep me guessin''
</p>
<p class="pre-chorus">
Ooh<br>
You are my muse<br>
I feel so reckless<br>
Oh, you''re makin'' me, makin'' me, makin'' me give in
</p>
<p class="chorus">
Oh, baby, I can feel the rush of adrenalin<br>
I''m not scared to jump if you want to<br>
Let''s just fall in love for the hell of it<br>
Maybe we''ll just keep fallin''<br>
I can feel the rush of adrenaline<br>
I''m not scared to jump ''cause I want you<br>
Let''s just fall in love for the hell of it<br>
Maybe we''ll just keep fallin''
</p>
<p class="verse">
When I''m around ya, I feel it in my veins<br>
There''s something about ya that''s makin'' me go insane<br>
We have a storm to weather<br>
My little sweet surrender
</p>
<p class="pre-chorus">
Ooh <span class="ad-lib">(Woah)</span><br>
You are my muse <span class="ad-lib">(You are my muse)</span><br>
I feel so reckless<br>
Oh, you''re makin'' me, makin'' me, makin'' me give in
</p>
<p class="chorus">
Oh, baby, I can feel the rush of adrenaline<br>
I''m not scared to jump if you want to<br>
Let''s just fall in love for the hell of it<br>
Maybe we''ll just keep fallin''<br>
I can feel the rush of adrenaline<br>
I''m not scared to jump ''cause I want you<br>
Let''s just fall in love for the hell of it<br>
Maybe we''ll just keep fallin''
</p>
<p class="post-chorus">
<span class="ad-lib">(Fallin'', fallin'', fallin'', fallin'')</span> Fall, fallin''<br>
<span class="ad-lib">(Fallin'', fallin'', fallin'', fallin'')</span> Fallin''<br>
<span class="ad-lib">(Fallin'', fallin'', fallin'', fallin'')</span> Fallin''<br>
Maybe, we''ll just keep fallin''
</p>
<p class="bridge">
Ooh<br>
You are my muse<br>
I feel so reckless<br>
Oh, you''re makin'' me, makin'' me, makin'' me, makin'' me<br>
Makin'' me, makin'' me, makin'' me give in
</p>
<p class="chorus">
Oh, baby, I can feel the rush of adrenaline<br>
I''m not scared to jump if you want to<br>
Let''s just fall in love for the hell of it<br>
Maybe we''ll just keep fallin'' <span class="ad-lib">(Just keep fallin'')</span><br>
I can feel the rush of adrenaline <span class="ad-lib">(Adrenaline, ooh)</span><br>
I''m not scared to jump ''cause I want you <span class="ad-lib">(I want you)</span><br>
Let''s just fall in love for the hell of it<br>
Maybe we''ll just keep fallin'' <span class="ad-lib">(We''ll just keep fallin'')</span>
</p>
<p class="outro">
<span class="ad-lib">(Fallin'', fallin'', fallin'', fallin'')</span> Yeah<br>
<span class="ad-lib">(Fallin'', fallin'', fallin'', fallin'')</span> We''ll just keep fallin''<br>
<span class="ad-lib">(Fallin'', fallin'', fallin'', fallin'')</span> Ooh, ooh<br>
Maybe we''ll just keep fallin''
</p>', 'https://open.spotify.com/artist/2jnIB6XdLvnJUeNTy5A0J2?si=mBvpmUYUT36VpGjlDODlzw', 'https://music.apple.com/us/artist/why-dont-we/1161814111', 'https://music.youtube.com/@whydontwe8155?si=rqwp_HvA8bUYQhs6', 1),
  (2, NULL, 'What am i', NULL, 'assets/audio/what-am-i.mp3', 'assets/image/album2.jpg', '<p class="verse">
I met her on a Monday evening<br>
She was standing outside of the park and ride<br>
And I said, "Hey ma, where you been?"<br>
"Been looking for a woman like you for all my life"<br>
And she said, "Hola, talk to me, before I have to leave"<br>
And one day we could be<br>
Maybe more than two lost souls just passing by
</p>
<p class="pre-chorus">
Knew I was falling when I looked inside your eyes<br>
She said, "I know you are, but what am I?"
</p>
<p class="chorus">
Tell me, have you seen a sunset<br>
Turn into a sunrise?<br>
Kiss right through the night?<br>
''Cause we should try that sometime<br>
Hold you ''til the mornin''<br>
And if I said I''m fallin'', would you just reply<br>
"I know you are, but what am I?"
</p>
<p class="post-chorus">
<span class="ad-lib">(Ah-ooh, ah-ooh)</span><br>
<span class="ad-lib">(Ah-ooh, ah-ooh)</span>
</p>
<p class="verse">
Open the door to my apartment, when we get in<br>
Kissin'' on my neck and throw my jacket to the side<br>
And I know your heart is beating quick<br>
And if you put your head on my chest, hear the same in mine, yeah<br>
And we''ll be making love<br>
Until the sun comes up<br>
But one day maybe I<br>
I could put a ring on your finger before you change your mind, yeah
</p>
<p class="pre-chorus">
Knew I was falling when I looked inside your eyes<br>
She said, "I know you are, but what am I?"
</p>
<p class="chorus">
Tell me, have you seen a sunset<br>
Turn into a sunrise?<br>
Kiss right through the night?<br>
''Cause we should try that sometime<br>
Hold you ''til the mornin''<br>
And if I said I''m fallin'', would you just reply<br>
"I know you are, but what am I?"
</p>
<p class="post-chorus">
<span class="ad-lib">(Ooh-ooh, ooh-ooh)</span><br>
What am I?<br>
<span class="ad-lib">(Ah-ooh, ah-ooh)</span>
</p>
<p class="bridge">
Tell me, have you seen a sunset<br>
Turn into a sunrise?<br>
Kiss right through the night?<br>
''Cause we should try that sometime<br>
Hold you ''til the mornin''<br>
And if I said I''m fallin'', would you just reply<br>
"I know you are, but what am I?"
</p>
<p class="chorus">
Tell me, have you seen a sunset <span class="ad-lib">(Yeah)</span><br>
Turn into a sunrise?<br>
Kiss right through the night? <span class="ad-lib">(The night)</span><br>
''Cause we should try that sometime <span class="ad-lib">(Try that sometime)</span><br>
Hold you ''til the mornin'' <span class="ad-lib">(Hold you ''til the morning)</span><br>
And if I said I''m fallin'', would you just reply<br>
"I know you are, but what am I?"
</p>
<p class="post-chorus">
What am I?<br>
What am I?<br>
Oh, what am I?<br>
What am I?<br>
What am I?
</p>
<p class="outro">
What am I?
</p>', 'https://open.spotify.com/artist/2jnIB6XdLvnJUeNTy5A0J2?si=mBvpmUYUT36VpGjlDODlzw', 'https://music.apple.com/us/artist/why-dont-we/1161814111', 'https://music.youtube.com/@whydontwe8155?si=rqwp_HvA8bUYQhs6', 1),
  (3, NULL, 'trust fund baby', NULL, 'assets/audio/trust-fund-baby.mp3', 'assets/image/trust-fund-baby.jpg', '<p class="verse">
I don''t want a girl who gets a car for her sweet 16<br>
Or spends a stack of dollar bills on a limousine<br>
I want a girl who takes the bus and who wears baggy jeans<br>
Rockin'' Nike Airs, what the hell are Louboutins?<br>
Don''t want no fake tan, short skirt, daddy''s money don''t work<br>
Shop until you drop on the town<br>
I want a smart girl, stronger than her father<br>
Someone who will laugh and tryna fit in the crowd <span class="ad-lib">(ah-ow, ow)</span>
</p>
<p class="pre-chorus">
And all we used to dream about<br>
Is getting rich and getting out<br>
Move to the nicer part of town<br>
Where we''d have numbers on our house<br>
It took a while to figure out<br>
What type of girl that I''m about<br>
Who brings the real man out of me, yah
</p>
<p class="chorus">
I don''t really want no trust fund baby<br>
I like my women independent<br>
And I say to people, "That''s my lady"<br>
And we don''t need nothing e-e-e-else<br>
I don''t want no trust fund baby<br>
Save your money, don''t spend it<br>
And I say to people, "That''s my lady"<br>
And we don''t need nothing e-e-e-else <span class="ad-lib">(I don''t want no-)</span>
</p>
<p class="verse">
Don''t want a girl who take selfies, want her makeup free<br>
Don''t want no mean girl lady, a pink prom Queen<br>
I want a girl who climbs trees, always dirt on her jeans<br>
Her daddy told her how to fix cars, maybe she could fix me<br>
I want a throwback kid who loves Missy Elliot<br>
Who for my birthday, makes a mixtape and puts it on cassette<br>
Want a girl with common sense, who''s dripping in competence<br>
Don''t wanna die to get rich, but she loves 50 Cent <span class="ad-lib">(ah-ow, ow)</span>
</p>
<p class="pre-chorus">
And all we used to dream about<br>
Is getting rich and getting out<br>
Move to the nicer part of town<br>
Where we''d have numbers on our house<br>
It took a while to figure out<br>
What type of girl that I''m about<br>
Who brings the real man out of me, yah
</p>
<p class="chorus">
I don''t really want no trust fund baby<br>
I like my women independent<br>
And I say to people, "That''s my lady"<br>
And we don''t need nothing e-e-e-else<br>
I don''t want no trust fund baby<br>
Save your money, don''t spend it<br>
And I say to people, "That''s my lady"<br>
And we don''t need nothing e-e-e-else
</p>
<p class="post-chorus">
I don''t want no- <span class="ad-lib">(no, I don''t want me)</span><br>
I don''t want no trust fund baby <span class="ad-lib">(no, I don''t want me)</span><br>
I just want me a proper lady <span class="ad-lib">(no, I don''t want me)</span><br>
I don''t want no trust fund baby <span class="ad-lib">(no, I don''t want me)</span>
</p>
<p class="chorus">
I don''t really want no trust fund baby<br>
I like my women independent <span class="ad-lib">(hey!)</span><br>
And I say to people, "That''s my lady" <span class="ad-lib">(that''s my lady, na-na-na-na-na)</span><br>
And we don''t need nothing e-e-e-else
</p>
<p class="outro">
I don''t want no- <span class="ad-lib">(no, I don''t want me)</span><br>
I don''t want no trust fund baby <span class="ad-lib">(no, I don''t want me)</span><br>
I just want me a proper lady <span class="ad-lib">(no, I don''t want me)</span><br>
I don''t want no trust fund baby <span class="ad-lib">(no, I don''t want me)</span><br>
<br>
No, I don''t want me
</p>', 'https://open.spotify.com/artist/2jnIB6XdLvnJUeNTy5A0J2?si=mBvpmUYUT36VpGjlDODlzw', 'https://music.apple.com/us/artist/why-dont-we/1161814111', 'https://music.youtube.com/@whydontwe8155?si=rqwp_HvA8bUYQhs6', 1),
  (4, NULL, '8 Letters', NULL, 'assets/audio/wdw.mp3', 'assets/image/8letters.jpg', '<p class="verse">
You know my worst, see me hurt, but you don''t judge<br>
That right there is the scariest feeling<br>
Opening and closing up again<br>
I''ve been hurt, so I don''t trust<br>
Now here we are, staring at the ceiling
</p>
<p class="pre-chorus">
I''ve said those words before, but it was a lie<br>
And you deserve to hear them a thousand times
</p>
<p class="chorus">
If all it is is eight letters<br>
Why is it so hard to say?<br>
If all it is is eight letters<br>
Why am I in my own way?<br>
Why do I pull you close?<br>
And then ask you for space<br>
If all it is is eight letters<br>
Why is it so hard to say?
</p>
<p class="verse">
Isn''t it amazing how almost every line on our hands align?<br>
When your hand''s in mine<br>
It''s like I''m whole again, isn''t that a sign?<br>
I should speak my mind
</p>
<p class="pre-chorus">
I''ve said those words before, but it was a lie<br>
And you deserve to hear them a thousand times
</p>
<p class="chorus">
If all it is is eight letters<br>
Why is it so hard to say?<br>
If all it is is eight letters<br>
Why am I in my own way?<br>
Why do I pull you close?<br>
And then ask you for space<br>
If all it is is eight letters<br>
Why is it so hard to say? <span class="ad-lib">(Woah, oh yeah)</span>
</p>
<p class="bridge">
When I close my eyes<br>
It''s you there in my mind<br>
When I close my eyes
</p>
<p class="chorus">
If all it is is eight letters<br>
Why is it so hard to say?<br>
If all it is is eight letters<br>
Why am I in my own way?<br>
Why do I pull you close?<br>
And then ask you for space<br>
If all it is is eight letters<br>
Why is it so hard to say?<br>
If all it is is eight letters
</p>
<p class="bridge">
When I close my eyes<br>
It''s you there in my mind <span class="ad-lib">(it''s only you)</span><br>
When I close my eyes<br>
<br>
If all it is is eight letters<br>
<br>
When I close my eyes<br>
It''s you there in my mind <span class="ad-lib">(you)</span><br>
When I close my eyes<br>
<br>
If all it is is eight letters
</p>', 'https://open.spotify.com/artist/2jnIB6XdLvnJUeNTy5A0J2?si=mBvpmUYUT36VpGjlDODlzw', 'https://music.apple.com/us/artist/why-dont-we/1161814111', 'https://music.youtube.com/@whydontwe8155?si=rqwp_HvA8bUYQhs6', 1);

INSERT IGNORE INTO videos (id, title, youtube_id, start_second) VALUES
  (1, 'Why Don''t We video 1', 'C3DlM19x4RQ', 108),
  (2, 'Why Don''t We video 2', 'ZxgbQz4n8-s', 108),
  (3, 'Why Don''t We video 3', 'fOmhbMXY52o', 108),
  (4, 'Why Don''t We video 4', 'vgx-R_4s12U', 108),
  (5, 'Why Don''t We video 5', 'Y15MOr9saxE', 108),
  (6, 'Why Don''t We video 6', 'vJF9kJxQpBY', 108),
  (7, 'Why Don''t We video 7', '5qUfjqksDic', 108),
  (8, 'Why Don''t We video 8', 'Oy8H4zUxWjg', 108);
