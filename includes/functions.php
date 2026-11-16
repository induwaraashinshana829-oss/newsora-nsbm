<?php
require_once __DIR__.'/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off']);
    session_start();
}
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function url(string $path=''): string { $script=str_replace('\\','/',$_SERVER['SCRIPT_NAME'] ?? '/index.php'); $base=dirname($script); if(basename($base)==='admin') $base=dirname($base); return rtrim($base,'/.').'/'.$path; }
function go(string $path): void { header('Location: '.url($path)); exit; }
function user(): ?array { if(empty($_SESSION['uid'])) return null; $s=db()->prepare('SELECT * FROM users WHERE id=?'); $s->execute([$_SESSION['uid']]); return $s->fetch(PDO::FETCH_ASSOC) ?: null; }
function require_user(): array { $u=user(); if(!$u) go('login.php'); return $u; }
function csrf(): string { return $_SESSION['csrf'] ?? ($_SESSION['csrf']=bin2hex(random_bytes(32))); }
function csrf_field(): void { echo '<input type="hidden" name="csrf" value="'.e(csrf()).'">'; }
function check_csrf(): void { if(!hash_equals(csrf(),(string)($_POST['csrf']??''))) { http_response_code(403); exit('Your session expired. Reload the page and try again.'); } }
function flash(string $text): void { $_SESSION['flash']=$text; }
function categories(): array { return ['Academic','Events','Sports','General']; }
function seed(): void {
 if((int)db()->query('SELECT COUNT(*) FROM articles')->fetchColumn()) return;
 $rows=[
 ['NSBM Marks Its Tenth Anniversary with Overnight Pirith Chanting','General','anniversary-pirith','An overnight Pirith Chanting Ceremony was held on 23 October 2026 at the newly constructed NSBM Civil Engineering Laboratory premises as part of the religious observances marking NSBM Green University’s tenth anniversary.

The ceremony invoked the blessings of the Triple Gem upon the NSBM community, including staff, students, alumni and parents, and sought blessings for the university’s continued progress and future endeavours.

A traditional procession escorted the venerable Maha Sangha to the decorated Pirith Mandapaya, accompanied by traditional drummers and flag-bearers.

Attendees included Vice Chancellor Prof. E. A. Weerasinghe; Senior Deputy Vice Chancellor Prof. Chaminda Rathnayake; Deputy Vice Chancellor – Academic Prof. Thilini De Silva; Head of Academic Development and Quality Assurance Prof. Baratha Dodankotuwa; faculty deans; heads of departments; academic and non-academic staff; and students.

Source: NSBM Green University — '],
 ['NSBM Literature Circle Hosts RANGAHALA ’26','Events','rangahala','RANGAHALA ’26, organised by the NSBM Literature Circle, was held on 9 July 2026 at the NSBM Open Air Theater. Under the theme “Echoes Through Time”, the event celebrated the evolution of Sri Lankan performance and literary arts through an evening of culture, creativity and entertainment.

One of the Literature Circle’s signature events, the celebration brought together an enthusiastic audience. Renowned musician Suneera Sumanga joined as Guest Artist, adding special performances to the evening.

The occasion was attended by Prof. E. A. Weerasinghe, Vice Chancellor; Prof. Chaminda Rathnayaka, Senior Deputy Vice Chancellor; Dr. Thilini D. Silva, Deputy Vice Chancellor – Academic; and Prof. Bharatha Dodankotuwa, Head of Academic Development and Quality Assurance, alongside academic and non-academic staff.

The organisers acknowledged the guidance and support of the Literature Circle’s Masters-in-Charge, Mr. Praveen Ranaweera and Mr. Savindu Damsara, whose leadership and encouragement helped make the event a success. Appreciation was also extended to all members of the NSBM Literature Circle.

Source: NSBM Green University — '],
 ['NSBM Dance and Aerobics Club Presents THRI-ANTHA','Events','thri-antha','The NSBM Dance and Aerobics Club announced THRI-ANTHA, presenting it as a story of love and an emotional journey for audiences.

The club shared a preview of the production on its official Facebook page.

Source: NSBM Dance and Aerobics Club — '],
 ['NSBM Dance and Aerobics Club Announces Kinetic Fire ’26','Events','kinetic-fire','The NSBM Dance and Aerobics Club has announced KINETIC FIRE ’26, a celebration of dance bringing together tradition, rhythm and expressive performance.

The club shared the event announcement on its official Facebook page, inviting audiences to look forward to the showcase.

Source: NSBM Dance and Aerobics Club — '],
 ['NSBM Computing Faculty Launches FIT Programme Session Two','Academic','fit-two','The Faculty of Computing at NSBM Green University launched the second session of the NSBM FIT (Future Innovation & Technology) Programme for Plymouth Batch 13 final-year Computing students, connecting them with industry knowledge and emerging technologies.

The session featured IFS experts Mr. Sujith Wijerathna, Senior Director, Software Engineering; Mr. Chamath De Silva, Principal DevOps Engineer; and Mr. Nuwan Ranasinghe, Senior DevOps Engineer. Drawing on their professional experience, the session focused on cloud technologies, DevOps practices, automation, modern software deployment and real-world software engineering workflows.

Ms. Pavithra Subhashini, Head of the Industry Collaboration Unit, Faculty of Computing, and Mr. Diluka Wijesinghe, Course Director for Plymouth Computing Degree Programmes, attended the event. Their participation reflected the faculty’s commitment to industry–academic partnerships and industry-relevant learning.

Through the FIT Programme, the Faculty of Computing aims to equip future professionals with practical perspectives, technical knowledge and industry exposure for a rapidly evolving technology sector.

Source: NSBM Green University — '],
 ['NSBM Engineering Undergraduates Participate in Techno 2026','Academic','techno-2026','Undergraduate students from the Faculty of Engineering at NSBM Green University participated in Techno 2026, the 38th National Engineering and Technology Exhibition organised by the Institution of Engineers, Sri Lanka (IESL).

Under the theme “Engineering for a Resilient Nation”, the exhibition provided a platform to explore engineering innovations, emerging technologies and practical solutions to real-world challenges.

The experience encouraged students to broaden their knowledge, exchange ideas and discover new possibilities for a more innovative and sustainable future. It also gave them an opportunity to engage with Sri Lanka’s engineering and technology community as they prepare for their future careers.

NSBM expressed its pride in its undergraduates’ participation and commitment to innovation and the future of engineering.

Source: NSBM Green University — '],
 ['NSBM Corporate Readiness Workshop Explores AI for Students','Academic','ai-students','The Department of Accounting and Finance at NSBM Green University conducted Session 04 of its Corporate Readiness Workshop Series for Batch 25.1 students under the theme “AI for Students”.

The session was facilitated by Mr. Charith Gunasekara, CFA, Associate Director at Uzabase Sri Lanka. He introduced students to the potential of Artificial Intelligence to enhance learning, improve productivity and help them navigate an evolving professional landscape.

Students explored how to use AI tools effectively and responsibly to support their academic work and prepare for the demands of the modern workplace.

The department expressed its appreciation to Mr. Charith Gunasekara and Uzabase Sri Lanka for supporting the development of students’ digital skills and professional readiness.

Source: NSBM Green University — '],
 ['NSBM Hosts Critical Literature Review Workshop for PhD Students','Academic','literature-review','NSBM Green University’s Faculty of Postgraduate and Professional Advancement hosted a two-day Critical Literature Review workshop for PhD students, facilitated by Dr. Payal Kumar, Principal Academic Advisor at the Indian School of Hospitality, India. The sessions helped doctoral researchers explore critical engagement with scholarly literature and its role in rigorous research.

Drawing on her experience in research, academic publishing and scholarly reviewing, Dr. Kumar discussed how to develop a critical approach to literature reviews. She emphasised thoughtful analysis and scholarly engagement as foundations for doctoral study.

Master of Education students also engaged with Dr. Kumar’s insights and considered their relevance to postgraduate study and educational research. Newly admitted PhD students met her as well, gaining an early opportunity to connect with an experienced scholar at the start of their doctoral journey.

Source: NSBM Green University — '],
 ['NSBM Orientation Day Two Focuses on Personal Development and Wellbeing','Academic','orientation-two','The second day of NSBM Green University’s new intake orientation programme focused on personal development, ethical responsibility and mental health.

The university shared highlights of the programme on its official Facebook page.

Source: NSBM Green University — '],
 ['NSBM Hosts Palliative Care Nursing Workshop for Final-Year Undergraduates','Academic','nursing-workshop','The Department of Health Sciences, Faculty of Science at NSBM Green University, held a Palliative Care Nursing Workshop for final-year nursing undergraduates on 14 October 2026. The workshop formed part of the Palliative and Geriatric Care Nursing module.

The sessions focused on holistic, compassionate and patient-centred care. Topics included patient assessment, pain and symptom management, non-pharmacological pain relief, end-of-life and bereavement care, and therapeutic communication.

The facilitators were Ms. Samindra Ranasinghe, Palliative Care Master Trainer and Matron of Shantha Sevena Hospice; Ms. Priyanka Kasthuriarachchi, Palliative Care Master Trainer at the National Institute of Cancer; Ms. Ajitha Nimali, Nurse-in-Charge of its Pain Management Unit; and Ms. W. A. K. Kumari, Nursing Officer at its Palliative Care Unit.

Ms. Sandyani de Silva, Senior Grade Nursing Tutor and Programme Coordinator of the B.Sc. (Hons) Nursing Degree Programme at NSBM, facilitated the learning opportunity and connected students with the visiting professionals.

The initiative supported clinical learning and the preparation of future nurses to deliver dignified, evidence-based care.

Source: Supplied workshop report and Facebook post — '],
 ['NSBM Drama Club Presents Megha 26','Events','drama-2026','NSBM Drama Club presented a 2026 stage event, featured in a post by the official NSBM Green University Facebook page. The shared performance clip shows dancers in traditional costumes performing on a decorated stage.

Source: NSBM Green University Facebook page, as shown in the supplied event screenshot.
Photo: Pathum L Weerasinghe / NSBM Drama Club.'],
 ['NSBM Retains KDU Inter-University T20 Cricket Championship','Sports','cricket-champions','NSBM won the KDU Leaders’ Trophy Inter-University T20 Cricket Championship 2026, completing the tournament unbeaten and securing a second consecutive inter-university cricket title.

In the final, NSBM restricted SLIIT to 130/9. Punsara Rajakaruna took three wickets for 23 runs. Ushen Silva’s unbeaten 63 and Lahiru Abeysinghe’s 44 off 21 balls helped NSBM reach the target with five wickets and nine deliveries remaining.

Ushen Silva received the Player of the Final award, while Amoda Vidanapathirana was named Player of the Tournament after contributing 169 runs and three wickets across four matches.

Source: NSBM Green University — '],
 ['Team Phoenix Wins NSBM Inter Batch 4.0 Chess Tournament','Sports','chess','More than 60 students took part in Inter Batch 4.0, the NSBM Chess Club’s largest inter-batch tournament to date, according to the university’s report published on 16 September 2026.

Team Phoenix (26.1) took first place, followed by Team A (26.2) in second and Team Apex (26.1) in third. The competition gave students an opportunity to demonstrate their strategy, concentration and sportsmanship through a series of closely contested matches.

Source: NSBM Green University — '],
 ['NSBM Faculty of Science Celebrates Its Fourth Anniversary','General','science-anniversary','The Faculty of Science at NSBM Green University celebrated its fourth anniversary on 28 April 2026 at the university auditorium. Academics, industry representatives and students gathered to mark the faculty’s progress.

The programme included a formal session called SciVision ’26, bringing the faculty community together to celebrate its achievements and engage in discussions. University leaders and representatives from several faculties attended the event.

Source: NSBM Green University — '],
 ['NSBM Unveils Proposed Medical and Research Hospital Model','General','hospital-model','NSBM Green University unveiled the architectural model for a proposed multibillion-rupee Medical and Research Hospital on 26 October 2026, during its tenth anniversary celebrations.

The project is planned for a 20-acre site opposite the university in Pitipana, Homagama, as part of its Phase III expansion. Proposed services include inpatient and outpatient care, diagnostics, wellness services and research-based healthcare.

The initiative forms part of NSBM NEXT, the university’s vision for its next decade, connecting healthcare, research, science and technology. The announcement concerns a proposed hospital and the unveiling of its model.

Source: NSBM Green University — '],
 ['NSBM Opens Rs. 720 Million Civil Engineering Laboratory Complex','General','civil-opening','NSBM Green University opened its new Civil Engineering Laboratory Complex on 26 October 2026 during its tenth anniversary celebrations. The university funded the Rs. 720 million investment in practical engineering education.

Located at the Phase II development in Pitipana, Homagama, the complex provides laboratories and learning facilities to support hands-on training and industry-relevant engineering skills.

Attorney General K. A. Parinda Ranasinghe Jr. ceremonially opened the complex. The development forms part of NSBM’s wider programme to expand its academic infrastructure.

Source: NSBM Green University — '],
 ['NSBM Celebrates Its 10th Anniversary','General','anniversary','NSBM Green University marked its tenth anniversary on 26 October 2026 with a ceremony at its auditorium in Homagama. The event brought together international university representatives, industry partners, alumni and other invited guests.

The celebration included a partnership agreement with Zhejiang Shuren University in China. A commemorative stamp, the Vision to Legacy magazine and an economic impact report covering 2016–2026 were also launched.

NSBM NEXT, a roadmap for the coming decade, outlined the university’s future direction in higher education and international collaboration.

Source: NSBM Green University — '],
 ['NSBM Hockey Teams Earn Podium Finishes at KDU Leaders’ Trophy 2026','Sports','hockey','NSBM Green University’s women’s and men’s hockey teams achieved podium finishes at the KDU Leaders’ Trophy 2026 Inter-University Sports Championship. The women’s team finished as first runner-up, while the men’s team secured second runner-up.

The teams were coached by Mr. Shanaka. Their performances highlighted the teamwork and preparation behind NSBM’s participation in the championship.

Source: NSBM Green University — '],
 ];
 $s=db()->prepare('INSERT INTO articles(title,category,image,body,status) VALUES(?,?,?,?,?)');
 foreach($rows as [$title,$category,$image,$body]) $s->execute([$title,$category,(['anniversary-pirith'=>'images/nsbm-pirith-ceremony-cover.jpg','rangahala'=>'images/nsbm-rangahala-26.jpg','thri-antha'=>'images/nsbm-thri-antha.jpg','kinetic-fire'=>'images/nsbm-kinetic-fire-26.jpg','fit-two'=>'images/nsbm-fit-session-two.jpg','techno-2026'=>'images/nsbm-techno-2026.jpg','ai-students'=>'images/nsbm-ai-for-students.jpg','literature-review'=>'images/nsbm-literature-review-workshop.jpg','orientation-two'=>'images/nsbm-orientation-day-two.jpg','nursing-workshop'=>'images/nsbm-palliative-nursing-workshop.jpg','drama-2026'=>'images/nsbm-megha-26.jpg','cricket-champions'=>'images/nsbm-cricket-champions-2026.jpg','chess'=>'images/nsbm-chess-interbatch.jpg','science-anniversary'=>'images/nsbm-science-anniversary.jpg','hospital-model'=>'images/nsbm-hospital-model.jpg','civil-opening'=>'images/nsbm-civil-opening.jpg','anniversary'=>'images/nsbm-10th-anniversary.jpg','research'=>'images/academic-students.jpg','career'=>'images/events-cultural.jpg','hockey'=>'images/nsbm-hockey-kdu-2026.jpg','dining'=>'images/nsbm-campus.jpg','faculty'=>'images/academic-students.jpg','drone'=>'images/academic-students.jpg','lecture'=>'images/events-cultural.jpg'][$image]??'images/nsbm-campus.jpg'),$body,'approved']);
}
seed();
function articles(string $where="status = 'approved' AND (format != 'Announcement' OR expires IS NULL OR expires = '' OR expires >= date('now'))",array $params=[]): array { $s=db()->prepare('SELECT articles.*, COALESCE(users.name,\'Campus Editorial Board\') AS author FROM articles LEFT JOIN users ON users.id=articles.user_id WHERE '.$where.' ORDER BY articles.id DESC'); $s->execute($params); return $s->fetchAll(PDO::FETCH_ASSOC); }
function saved(int $id): bool { if(!user()) return false; $s=db()->prepare('SELECT 1 FROM bookmarks WHERE user_id=? AND article_id=?'); $s->execute([user()['id'],$id]); return (bool)$s->fetchColumn(); }
function icon(string $name): string {
 $paths=['home'=>'M3 10 12 3l9 7v11h-6v-7H9v7H3Z','browse'=>'M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18m4 5-2 6-6 2 2-6Z','announce'=>'M3 10v5h4l11 5V5L7 10H3m4 5 2 6m12-11 3-2m-3 7 3 2','article'=>'M5 3h11l4 4v14H5Zm10 0v5h5M8 12h9M8 16h9','saved'=>'M6 3h12v18l-6-4-6 4Z','subscriptions'=>'M3 5h18v14H3Zm0 0 9 8 9-8','submissions'=>'m4 12 5 5L20 6','settings'=>'M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8M12 2v3m0 14v3M2 12h3m14 0h3M5 5l2 2m10 10 2 2M5 19l2-2M17 7l2-2','search'=>'M10 3a7 7 0 1 0 0 14 7 7 0 0 0 0-14m5 12 6 6','bell'=>'M6 8a6 6 0 0 1 12 0v8l2 2H4l2-2Zm4 13h4','academic'=>'m2 8 10-5 10 5-10 5Zm4 3v6l6 4 6-4v-6','calendar'=>'M4 5h16v16H4ZM4 10h16M8 2v6m8-6v6','sports'=>'M7 3h10v8a5 5 0 0 1-10 0Zm0 2H3v4a4 4 0 0 0 4 4m10-8h4v4a4 4 0 0 1-4 4m-5 3v5m-5 0h10','user'=>'M12 3a4 4 0 1 0 0 8 4 4 0 0 0 0-8m-8 18v-3a8 6 0 0 1 16 0v3Z','comment'=>'M3 4h18v13H8l-5 4Z','arrow'=>'M4 12h16m-6-6 6 6-6 6'];
 return '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
<path d="'.($paths[$name]??$paths['article']).'"/>
</svg>';
}
function logo(): string { return '<span class="logo logo-image"><img src="'.e(url('images/newsora-logo-white.jpg')).'" alt="Newsora" width="1620" height="1080"></span>'; }
function bookmark_button(array $a): void { echo '<button type="button" class="bookmark '.(saved((int)$a['id'])?'is-saved':'').'" data-bookmark="'.(int)$a['id'].'" aria-label="Save article" aria-pressed="'.(saved((int)$a['id'])?'true':'false').'">'.icon('saved').'</button>'; }
function card(array $a, string $kind=''): void { ?>
<article class="news-card <?=e($kind)?>">
<div class="card-image <?=($a['image']??'')==='images/nsbm-megha-26.jpg'?'full-photo ':''?><?=($a['image']??'')==='images/academic-students.jpg'?'academic-photo':(($a['image']??'')==='images/events-cultural.jpg'?'events-photo':'')?>">
<a href="<?=url('article.php?id='.$a['id'])?>">
<img src="<?=url($a['image'])?>" alt="<?=e($a['title'])?>" loading="lazy">
</a>
<?php bookmark_button($a); ?>
</div>
<div class="card-body">
<span class="tag">
<?=e($a['category'])?>
</span>
<h3>
<a href="<?=url('article.php?id='.$a['id'])?>">
<?=e($a['title'])?>
</a>
</h3>
<p>
<?=e($a['body'])?>
</p>
<div class="card-meta">
<span>By <?=e($a['author'])?>
</span>
<span>
<?=date('M j',strtotime($a['created_at']))?> · 5 min read</span>
</div>
</div>
</article>
<?php }
