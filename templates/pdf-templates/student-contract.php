<?php
/**
 * Template Name: Student Contract (Bachelor)
 * Template ID: student-contract
 * Description: Template for university study contracts in Romanian
 * Required Fields: number, date, nameRo, birthDate, country, city, documentType, seria, documentNumber, documentId, phone, email, faculty, program, yearsNumber, yearsLetters, price
 * Default Font: cambria
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Получаем пути к изображениям
$plugin_dir = plugin_dir_path(dirname(__FILE__, 2));
$uploads_dir = wp_upload_dir();
$headerImage = $plugin_dir . 'assets/images/udg_doc_header.png';
$secondHeader = $plugin_dir . 'assets/images/headerSecond.png';
$signImage = $plugin_dir . 'assets/images/Sign.png';
$stampImage = $plugin_dir . 'assets/images/stamp.png';

// Проверяем наличие файлов
$headerImage = file_exists($headerImage) ? $headerImage : '';
$secondHeader = file_exists($secondHeader) ? $secondHeader : '';
$signImage = file_exists($signImage) ? $signImage : '';
$stampImage = file_exists($stampImage) ? $stampImage : '';

// Получаем данные из CSV
$contract_number = isset($row['number']) ? htmlspecialchars($row['number']) : '';
$contract_date = isset($row['date']) ? htmlspecialchars($row['date']) : date('d.m.Y');
$student_name_ro = isset($row['nameRo']) ? htmlspecialchars($row['nameRo']) : '';
$birth_date = isset($row['birthDate']) ? htmlspecialchars($row['birthDate']) : '';
$country = isset($row['country']) ? htmlspecialchars($row['country']) : '';
$city = isset($row['city']) ? htmlspecialchars($row['city']) : '';
$document_type = isset($row['documentType']) ? htmlspecialchars($row['documentType']) : '';
$seria = isset($row['seria']) ? htmlspecialchars($row['seria']) : '';
$document_number = isset($row['documentNumber']) ? htmlspecialchars($row['documentNumber']) : '';
$document_id = isset($row['documentId']) ? htmlspecialchars($row['documentId']) : '';
$phone = isset($row['phone']) ? htmlspecialchars($row['phone']) : '';
$email = isset($row['email']) ? htmlspecialchars($row['email']) : '';
$faculty = isset($row['faculty']) ? htmlspecialchars($row['faculty']) : '';
$program = isset($row['program']) ? htmlspecialchars($row['program']) : '';
$years_number = isset($row['yearsNumber']) ? htmlspecialchars($row['yearsNumber']) : '';
$years_letters = isset($row['yearsLetters']) ? htmlspecialchars($row['yearsLetters']) : '';
$price = isset($row['price']) ? htmlspecialchars($row['price']) : '';

// Определяем общее количество страниц для использования в шаблоне
$total_pages = 7;

// Создаем HTML-код для футера (будет установлен в pdf-generator.php)
$footer_html = '<div style="text-align: center; font-size: 10px;">Pagina {PAGENO} din ' . $total_pages . '</div>';

// CSS стили
$css = "
body {
    font-family: Cambria, DejaVu Serif, serif;
    font-size: 12px;
    line-height: 1;
    color: #000;
    margin: 0;
    padding: 0;
}
h1 {
    line-height: 1.15;
    text-align: center;
    color: #000;
    margin-bottom: 15px;
    font-size: 16px;
    font-weight: bold;
}
h2 {
    line-height: 1.15;
    text-align: center;
    color: #000;
    margin-bottom: 10px;
    font-size: 14px;
    font-weight: bold;
}
h3 {
    line-height: 1;
    color: #000;
    margin-bottom: 8px;
    font-size: 12px;
    font-weight: bold;
}
.meta-data{
    margin:0;
    text-align: center;
}
p {
    line-height: 1;
    margin: 2px;
    text-align: justify;
    font-size: 14px;
}
strong {
    color: #000;
    font-weight: bold;
}
img {
    width: 100%;
    max-width: 100%;
}
.page {
    position: relative;
    padding: 15px;
    height: 100%;
    min-height: 95vh;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
}
.page-break {
    page-break-before: always;
}
.content {
    padding: 0 20px;
    flex: 1;
}
.section {  
    line-height: 1;
    margin-bottom: 10px;
}
.subsection {
    line-height: 1;
    margin-left: 15px;
    font-size:14px;
}

.list-item {
    line-height: 1;
    margin-bottom: 5px;
    font-size:14px;
}
.page-number {
    position: absolute;
    bottom: 10px;
    left: 0;
    right: 0;
    text-align: center;
    font-size: 10px;
    padding: 5px;
}

.meta-data{
    font-size:14px;
    font-weight: normal;
}

/* --- signature block for page 6 (mpdf-friendly) --- */
.signature-block{
    margin-top: 36px;
    width: 100%;
    box-sizing: border-box;
}

.sig-left {
    width: 300px;
    float: left; 
    box-sizing: border-box; 
    text-align: left;
    font-family: Cambria, DejaVu Serif, serif;
}

.sig-right{
    width: 300px;
    float: right;
    box-sizing: border-box; 
    text-align: right;
    font-family: Cambria, DejaVu Serif, serif;
}

.sig-right p{
    text-align: right;
}
.sig-left p{
    text-align: left;
}
.sig-right h1{
    text-align: right;
    margin:0;
}
.sig-left h1{
    text-align: left;
    margin:0;
}

";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Contract de Studii - <?php echo $student_name_ro; ?></title>
    <style><?php echo $css; ?></style>
</head>
<body>
    <!-- Page 1 -->
    <div class="page">
        <?php if ($headerImage): ?>
            <img src="<?php echo $headerImage; ?>" alt="University Header">
        <?php endif; ?>
        <div class="content">
            <h1>CONTRACT DE STUDII<br>SUPERIOARE Ciclul I - EQF 6 <br><span class="meta-data"> Nr. <?php echo $contract_number; ?> din <?php echo $contract_date; ?></span></h1>
            </div>
            
            <div class="section">
                <h2>I. PĂRȚILE CONTRACTULUI</h2>
                <p>IR Universitatea "Divitia Gratiae" cu sediul în mun. Chișinău, str. Al. Hajdeu, 94, c/f 1013620007488, înregistrată la Ministerul Justiției sub nr. 741 din 18 iulie 2013, reprezentată prin Rectorul Universității, d-nul Malancea Iurie, denumită în continuare <strong>UNIVERSITATE</strong> și <strong><?php echo $student_name_ro; ?></strong>, născut(ă) la data de <strong><?php echo $birth_date; ?></strong>, domiciliat(ă): țara <strong><?php echo $country; ?></strong>, localitatea <strong><?php echo $city; ?></strong>, identificat(ă) cu actul de identitate <strong><?php echo $document_type; ?></strong>, seria <strong><?php echo $seria; ?></strong>, nr. <strong><?php echo $document_number; ?></strong>, IDNP <strong><?php echo $document_id; ?></strong>, telefon <strong><?php echo $phone; ?></strong>, e-mail <strong><?php echo $email; ?></strong>, în calitate de student(ă) la Facultatea de <strong><?php echo $faculty; ?></strong>, programul de studii <strong><?php echo $program; ?></strong>, în continuare <strong>STUDENT</strong>, au încheiat prezentul contract.</p>
            </div>

            <div class="section">
                <h2>II. OBIECTUL CONTRACTULUI</h2>
                <p>Prezentul contract are ca obiect derularea activităților de învățământ, reglementând raporturile dintre UNIVERSITATE, instituție de învățământ superior prestatoare de servicii educaționale, și STUDENT, beneficiarul de servicii educaționale, cu precizarea drepturilor și obligațiilor părților semnatare, în concordanță cu prevederile statutare și hotărârile conducerii universității.</p>
            </div>

            <div class="section">
                <h2>III. DURATA CONTRACTULUI</h2>
                <p><strong>3.1.</strong> Prezentul contract se încheie pe o durată de <strong><?php echo $years_number; ?> (<?php echo $years_letters; ?>) ani</strong>, așa cum este prevăzută aceasta în actele normative în vigoare, începând cu anul universitar.</p>
                <p><strong>3.2.</strong> Posibilele modificări impuse de legislație sau de regulamentul de activitate didactică al Universității vor fi stipulate într-o anexă. De asemenea, orice modificare (schimbarea tipului finanțării, întreruperi, prelungiri etc.), intervenită pe parcursul studiilor universitare de licență, va fi consemnată într-un Act adițional.</p>
                <p><strong>3.3.</strong> STUDENTUL care nu finalizează programul de studii în durata normală, ca urmare a repetenției, a reluării studiilor în caz de întrerupere, sau a reînmatriculării după exmatriculare sau de retragere, va solicita obligatoriu încheierea unui nou contract de studii, în condițiile stabilite de universitate la data încheierii acestuia.</p>
            </div>
            <div class="section">
                <h2>IV. DREPTURILE ȘI OBLIGAȚIILE PĂRȚILOR</h2>
                
                <h3>4.1. Drepturile Universității:</h3>
                <div class="list-item">a) stabilește condițiile de înscriere, înmatriculare, întrerupere, exmatriculare, reînscriere și reînmatriculare la studii a studentului;</div>
                <div class="list-item">b) supraveghează și urmărește modul în care studentul își respectă obligațiile contractuale asumate prin prezentul contract;</div>
                <div class="list-item">c) supraveghează și urmărește modul în care studentul își respectă îndatoririle de student;</div>
                <div class="list-item">d) stabilește criteriile de ierarhizare anuală a studenților pe locurile sponsorizate în conformitate cu prevederile statutare, regulamente interne și alte structuri de conducere ale universității;</div>
                <div class="list-item">e) repartizează anual studenții pe locurile sponsorizate, locurile cu taxă, locurile în cămine, potrivit criteriilor aprobate de instituție;</div>
                <div class="list-item">f) stabilește cuantumul taxei de școlarizare și a celorlalte taxe;</div>
                <div class="list-item">g) aplică soluții de acoperire, ridică temporar sau definitiv facilitățile de care beneficiază studentul (bursă, cămin, alimentarea cu titlu gratuit, etc.) în cazul nerespectării regulamentelor, acordurilor și a disciplinei în cadrul instituției.</div>
            </div>
        </div>
    </div>

    <!-- Page 2 -->
    <div class="page page-break">
        <?php if ($secondHeader): ?>
            <img src="<?php echo $secondHeader; ?>" alt="University Header">
        <?php endif; ?>
        <div class="content">
            <div class="section">
                <h3>4.2. Obligațiile UNIVERSITĂȚII:</h3>
                <div class="list-item">a) organizează activități educaționale, inclusiv cele de practică și de verificare a cunoștințelor, la nivel universitar, în conformitate cu normele interne adoptate în baza specificului universitar, respectiv cu planul de învățământ, aprobat de către Senatul universitar;</div>
                <div class="list-item">b) încheie cu studentul, la începutul fiecărui an universitar, un act adițional la contractul de studii;</div>
                <div class="list-item">c) înscrie studentul în Registrul matricol al instituției;</div>
                <div class="list-item">d) eliberează actele de studii;</div>
                <div class="list-item">e) organizează și permite înscrierea studentului la examenul de finalizare a studiilor;</div>
                <div class="list-item">f) nu face distincție între studenții admiși pe locurile cu taxă și cei admiși pe locurile sponsorizate, în ceea ce privește calitatea procesului educațional;</div>
                <div class="list-item">g) aduce anual la cunoștința studenților, cu cel puțin 15 zile înaintea începerii anului universitar, cuantumul taxei pentru fiecare an de studiu, prin afișare la sediul facultății și pe pagina proprie de internet;</div>
                <div class="list-item">h) nu modifică valoarea taxelor de școlarizare în cursul unui an universitar;</div>
                <div class="list-item">i) evaluează, la începutul fiecărui an universitar, locurile prin sponsorizare ce vor intra în procedura de ierarhizare anuală a studenților;</div>
                <div class="list-item">j) asigură condițiile de exercitare a drepturilor studenților, în concordanță cu normele și practicile educaționale.</div>
                
                <h3>4.3. Drepturile STUDENTULUI:</h3>
                <div class="list-item">a) participă la activitățile didactice și de pregătire profesională prevăzute în planul de învățământ;</div>
                <div class="list-item">b) face parte din comunitatea universitară, în conformitate cu prevederile în vigoare;</div>
                <div class="list-item">c) susține, în sesiunile programate, examenele și celelalte forme de verificare a cunoștințelor dobândite;</div>
                <div class="list-item">d) susține, în sesiunile programate, examenele de finalizare a studiilor;</div>
                <div class="list-item">e) utilizează cu bună credință baza materială afiliată procesului educațional;</div>
                <div class="list-item">f) beneficiază de asistență și servicii complementare gratuite, în limita prevederilor normative;</div>
                <div class="list-item">g) beneficiază de libertatea de exprimare, cu respectarea limitelor legale;</div>
                <div class="list-item">h) beneficiază de loc de cazare în cămin, cu respectarea limitei capacității de cazare disponibile;</div>
                <div class="list-item">i) beneficiază de dreptul de restituire a taxei de școlarizare studenții înscriși în anul I de studii, care solicită retragerea înainte de emiterea deciziei de înmatriculare (30 zile după începerea anului de studii), cu aprobarea Rectorului;</div>
                <div class="list-item">j) beneficiază de toate drepturile, facilitățile și oportunitățile stabilite de structurile de conducere ale universității.</div>

                <h3>4.4. Obligațiile STUDENTULUI:</h3>
                <div class="list-item">a) depune în momentul completării contractului toate documentele de înscriere la admitere în original și o copie, conform prevederilor instituției;</div>
                <div class="list-item">b) depune în momentul completării contractului/actului adițional diploma de bacalaureat și suplimentul (după caz);</div>
                <div class="list-item">c) îndeplinește obligațiile asumate prin contractul de studii și prin orice alte contracte sau acorduri încheiate cu universitatea;</div>
                <div class="list-item">d) îndeplinește toate sarcinile ce îi revin potrivit planului de învățământ și fișelor disciplinelor, cu respectarea condițiilor de promovabilitate din cadrul universității, și anume:</div>
                <div class="subsection">• Promovarea unui an de studii din cadrul unui ciclu, necesită obținerea a minimum de 45 de credite (ECTS) din totalul celor 60 alocate/an, cu precizarea că numărul creditelor restante este de maximum 15 credite / an de studiu, dar nu mai mult de 20 de credite restante în interiorul ciclului (anii I și II pentru primul ciclu);</div>
                <div class="subsection">• Promovarea unui ciclu de studii presupune promovarea tuturor disciplinelor și a numărului total de credite prevăzute pentru fiecare program de studiu.</div>
                <div class="list-item">e) respectă legislația și toate reglementările adoptate de către structurile de conducere ale universității, îndeosebi cele referitoare la disciplină și etica universitară;</div>
                <div class="list-item">f) aduce la cunoștința conducerii Facultății orice situație de natură să atragă modificarea statutului de student;</div>
                <div class="list-item">g) achită taxa de studii și celelalte taxe stabilite și afișate anual de către UNIVERSITATE în cuantumul, modul și la termenul stabilit;</div>
                
            </div>
        </div>
    </div>

    <!-- Page 3 -->
    <div class="page page-break">
        <?php if ($secondHeader): ?>
            <img src="<?php echo $secondHeader; ?>" alt="University Header">
        <?php endif; ?>
        <div class="content">
            <div class="section">
                <div class="list-item">h) achită taxa de studii integral pe anul universitar în curs, în caz de retragere/întrerupere;</div>
                <div class="list-item">i) nu solicită restituirea taxelor achitate în cazul exmatriculării sau al transferului la alte instituții de învățământ;</div>
                <div class="list-item">j) completează și semnează actul adițional la contractul de studii universitare la începutul fiecărui an universitar, în termenul stabilit de conducerea facultății;</div>
                <div class="list-item">k) își dă consimțământul cu privire la prelucrarea datelor personale care demonstrează statutul de student înmatriculat, pentru a beneficia de asigurarea de sănătate și alte facilități aferente statutului de student conform prevederilor legale în vigoare;</div>
                <div class="list-item">l) își dă consimțământul cu privire la prelucrarea datelor personale în scopul exercitării drepturilor asigurate de calitatea de student sau absolvent, pe toată durata școlarității, respectiv la finalizarea studiilor;</div>
                <div class="list-item">m) cunoaște faptul că în incinta campusului universitar este interzisă păstrarea, traficul și consumul substanțelor și produselor alcoolice, narcotice, stupefiante, halucinogene, de nicotină și etnobotanice;</div>
                <div class="list-item">n) cunoaște, acceptă și semnează (după caz) Mărturisirea de credință a universității, Normele de conduită a studentului, Regulamente și alte acte normative ale Universității, și însușește modificările aduse acestora pe durata prezentului contract.</div>
            </div>

            <div class="section">
                <h2>V. PLATA ȘI CONDIȚIILE DE PLATĂ A TAXEI DE STUDII</h2>
                <p><strong>5.1.</strong> Cuantumul taxei de studii se stabilește anual de către organul abilitat al Universității și se aduce la cunoștință în condițiile prevăzute de prezentul contract.</p>
                <p><strong>5.2.</strong> Plata taxelor de studii aferente anului I de studii se achită în perioada stabilită prin metodologia proprie de admitere. Neplata taxei de studii în termenul stabilit de conducerea Universității va duce la pierderea locului obținut.</p>
                <p><strong>5.3.</strong> Taxa de studii pentru anul universitar este de <strong><?php echo $price; ?> MDL</strong>, conform taxelor de studii și alte taxe, și poate fi achitată în felul următor:</p>
                <div class="list-item">a) integral prin virament bancar/online, în contul IR Universitatea Divitia Gratiae, Cod fiscal: 1013620007488, deschis la BC 'Victoriabank' SA fil. N3 or. Chișinău, Cod IBAN: MD86VI225100000102243MDL, Codul băncii: VICBMD2X416 cu următoarele mențiuni obligatorii: „taxa de studii – numele și prenumele studentului";</div>
                <div class="list-item">b) parțial, prin virament bancar/on-line.</div>
                <p><strong>5.4.</strong> Studenții pot achita taxa de studii integral sau parțial în două tranșe egale, în termen de 15 de zile de la începerea anului universitar, respectiv de la începerea semestrului 2 (pentru studenții care achită taxa de școlarizare în două tranșe egale).</p>
                <p><strong>5.5.</strong> Neachitarea taxelor de studii în termenele și condițiile stabilite de universitate conduce la interdicția participării studentului la examene și conferă universității dreptul de a exmatricula studentul, cu toate consecințele aferente exmatriculării.</p>
                <p><strong>5.6.</strong> Studentul exmatriculat pentru neachitarea taxelor datorate se poate reînscrie în programe de studii oferite de universitate, doar în condițiile achitării debitelor datorate acesteia.</p>
                <p><strong>5.7.</strong> Taxa de studii nu include costurile aferente echipamentului și instrumentarului necesar pregătirii profesionale a studentului (laptop, birotică, etc.).</p>
            </div>
            <div class="section">
                <h2>VI. ÎNCETAREA ȘI REZILIEREA CONTRACTULUI</h2>
                <p><strong>6.1.</strong> Contractul de studii încetează la momentul finalizării studiilor. Obligațiile născute până la data încetării trebuie executate în condițiile contractuale.</p>
                <p><strong>6.2.</strong> Contractul de studii se reziliază de drept, în următoarele cazuri: retragerea de la studii, mobilitatea internă definitivă la altă instituție de învățământ superior și repetenția.</p>
                <p><strong>6.3.</strong> Contractul poate fi reziliat unilateral de universitate pentru neîndeplinirea obligațiilor de către student, prin exmatricularea acestuia. În acest caz, universitatea este îndreptățită la recuperarea datoriilor acumulate de către student până la data exmatriculării și/sau a unor daune materiale.</p>
                <p><strong>6.4.</strong> Prezentul contract încetează și în caz de forță majoră. Forța majoră este constatată de o autoritate competentă. Partea care o invocă are obligativitatea de a o aduce la cunoștința celeilalte părți, în scris, în maximum 5 zile calendaristice de la apariție, iar dovada forței majore se va comunica în cel mult 15 zile calendaristice de la apariția acesteia. Forța majoră apără de răspundere partea care o invocă, cealaltă parte neavând dreptul de a cere despăgubiri.</p>
            </div>
        </div>
    </div>

    <!-- Page 4 -->
    <div class="page page-break">
        <?php if ($secondHeader): ?>
            <img src="<?php echo $secondHeader; ?>" alt="University Header">
        <?php endif; ?>
        <div class="content">
            <div class="section">
                <h2>VII. ALTE CLAUZE</h2>
                <p><strong>7.1.</strong> Prin semnarea prezentului contract, studentul declară în mod expres acordul de prelucrare a datelor cu caracter personal, furnizarea datelor cu caracter personal partenerilor universității și acordul de înregistrare foto/video pe durata prezentului contract.</p>
                <p><strong>7.2.</strong> Prin semnarea prezentului contract, studentul declară că a făcut cunoștință cu conținutul mărturisirii de credință, al regulamentelor, al metodologiilor, al normelor de disciplină, al normelor de etică și deontologie universitară și al altor documente cu caracter normativ din cadrul UNIVERSITĂȚII.</p>
                <p><strong>7.3.</strong> Orice îngăduință din partea UNIVERSITĂȚII nu poate fi interpretată ca o renunțare la clauzele de exmatriculare stipulate.</p>
                <p><strong>7.4.</strong> Studentul se obligă să respecte prevederile Legii securității și sănătății în muncă.</p>
                <p><strong>7.5.</strong> În cazul apariției unor litigii izvorând din interpretarea, executarea sau rezilierea prezentului contract, care nu pot fi rezolvate pe cale amiabilă, părțile se vor adresa instanțelor competente.</p>
                <p><strong>7.6.</strong> Prezentul contract s-a încheiat la UNIVERSITATE, în 2 (două) exemplare, câte unul pentru fiecare parte contractantă.</p>
                <p><strong>7.7.</strong> Pentru UNIVERSITATE prezentul contract este semnat de către Rectorul UNIVERSITĂȚII.</p>
            </div>
            <div class="signature-block">
                <div class="sig-left">
                    <h1>RECTOR</h1>
                    <p style="margin-top:20px;">Dr. Malancea Iurie</p>
                    <p style="margin-top:40px;">Semnătura: _____________________</p>
                    <p style="margin-top:20px;">L. Ș.</p>
                </div>
                <div class="sig-right">
                    <h1>STUDENT</h1>
                    <p style="margin-top:40px;">N/P ___________________________________</p>
                    <p style="margin-top:40px;">Semnătura: _____________________</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Page 5 -->
    <div class="page page-break">
        <?php if ($secondHeader): ?>
            <img src="<?php echo $secondHeader; ?>" alt="University Header">
        <?php endif; ?>
        <div class="content">
            <h2 style="text-align: center;">ANEXE la <em>Contract de studii UDG<em></h2>
            <h2>Anexa 1: MĂRTURISIREA DE CREDINŢĂ</h2>
            <div class="section">
                <p style="text-align:center;">Mărturisirea noastră de credinţă este baza doctrinară a activităţii <br> Universităţii „Divitia Gratiae" din Chişinău.</p>
                
                <div class="list-item"><strong>1.</strong> Noi credem, că Sfânta Scriptură, prin divina şi neîndoielnica sa inspiraţie, este adevăratul Cuvânt al lui Dumnezeu şi absoluta autoritate în lucrarea de mântuire a sufletului omenesc şi în trăirea unei vieţi creştine.</div>
                
                <div class="list-item"><strong>2.</strong> Noi credem în caracterul veşnic şi trinitar al Dumnezeului Tată, Fiu, Duh Sfânt.</div>
                
                <div class="list-item"><strong>3.</strong> Noi credem că Isus Hristos, Fiul lui Dumnezeu, născut din fecioară, Care a dus pe pământ o viaţă fără păcat, a făcut minuni, Şi-a dat viaţa ca jertfă înlocuitoare pe Golgota, pentru ai împăca pe oameni cu Dumnezeu, Care a înviat în trup, S-a înălţat la cer şi stă la dreapta lui Dumnezeu Tatăl.</div>
                
                <div class="list-item"><strong>4.</strong> Noi credem că lucrarea de slujire a Duhului Sfânt este proslăvirea Fiului lui Dumnezeu, învinuirea lumii de păcat, renaşterea spirituală a credincioşilor şi înzestrarea lor cu daruri în vederea zidirii Trupului Domnului Isus Hristos, care este Biserica.</div>
                
                <div class="list-item"><strong>5.</strong> Noi credem, că omul este creat după chipul şi asemănarea lui Dumnezeu, dar ca urmare a căderii în păcat este despărţit de Creatorul său şi se află pe calea pierzării.</div>
                
                <div class="list-item"><strong>6.</strong> Noi credem, că răscumpărarea omului păcătos este cu putinţă numai prin jertfa ispăşitoare şi învierea Domnului nostru Isus Hristos. Această răscumpărare se poate dobândi numai prin credinţă personală în Fiul lui Dumnezeu.</div>
                
                <div class="list-item"><strong>7.</strong> Noi credem, că Biserica întruneşte pe credincioşii renăscuţi, botezaţi în baza mărturisirii credinţei lor.</div>
                
                <div class="list-item"><strong>8.</strong> Noi credem, că Biserica are o întreită poruncă: să-L proslăvească pe Dumnezeu, să-i zidească pe credincioşi şi să ducă Evanghelia lui Isus Hristos în toată lumea.</div>
                
                <div class="list-item"><strong>9.</strong> Noi credem că Hristos este capul Bisericii şi că Biserica este despărţită de stat. Biserica Universală îşi găseşte expresia în activitatea bisericilor locale, care are dreptul la autonomie.</div>
                
                <div class="list-item"><strong>10.</strong> Noi credem că Isus Hristos va veni în slavă pentru a-şi răpi Biserica şi a instaura eterna Împărăţie a lui Dumnezeu.</div>
                
                <div class="list-item"><strong>11.</strong> Noi credem că toţi oamenii vor fi înviaţi în trup: credincioşii în Hristos pentru slavă veşnică, necredincioşii pentru pierzare veşnică.</div>
                
                <div class="list-item"><strong>12.</strong> Noi credem că fiecare membru al Bisericii este chemat la o slujire personală, potrivit cu „Marea poruncă" a Domnului nostru Isus Hristos.</div>
                
                <p style="margin-top: 20px; text-align: center;"><em>După ce studentul a luat cunoștință cu Mărturisirea de credință respectivă, acesta semnează contractul de studii, asumându-și responsabilitatea acceptării acesteia.<em></p>
                <p style="margin-top: 20px;"><strong>(După caz) Semnătura (numele, prenumele) _______________________________&nbsp;&nbsp;&nbsp; Data _______________</strong></p>
            </div>
        </div>
    </div>

    <!-- Page 6 -->
    <div class="page page-break">
        <?php if ($secondHeader): ?>
            <img src="<?php echo $secondHeader; ?>" alt="University Header">
        <?php endif; ?>
        <div class="content">
            <div class="section">
                <h2>Anexa 2: NORMELE DE CONDUITĂ ALE STUDENTULUI</h2>
                <p style="text-align: right;"><em>Aprobat<br>la ședința Senatului UDG<br>din 30.08.2023,<br>Proces-verbal Nr. 14</em></p>
                
                <h2>I. DISPOZIȚII GENERALE</h2>
                <p>Universitatea „Divitia Gratiae" din Chișinău (în continuare UDG), pregătește slujitori creștini pentru Republica Moldova, țările din Europa de Est și Asia. Normele de conduită ale UDG au la bază principii biblice și implică respect fată de conducere, profesori, personalul administrativ și colegi.</p>
                <p>Studentul se obligă să respecte normele de conduită stipulate în acest document.</p>
                <p>Pentru a menține ordinea civică stabilită de Dumnezeu, UDG nu poate tolera nici o încălcare a legii. Studentul se obligă să respecte legislația Republicii Moldova în cazul în care legea nu contravine principiilor biblice.</p>
                
                <h2>II. PROCESUL DE ÎNVĂŢĂMÂNT</h2>
                <p>Studentul se obligă să frecventeze toate cursurile prevăzute în planul de învățământ, să îndeplinească sarcinile de curs, să susțină testele și examenele conform calendarului academic.</p>
                <p>Evaluarea rezultatelor învățării în Republica Moldova se face cu note de la 10 la 1 (nota 5 fiind considerată trecătoare) și, după caz, cu calificativele „excelent", „foarte bine", „bine", „satisfăcător", „nesatisfăcător", „admis", „respins". Termenul limită pentru reexaminare este primele două săptămâni din semestrul viitor. Reexaminarea poate avea loc maxim de trei ori și este aprobată în urma achitării de către student a taxei stabilite.</p>
                <p>Universitatea oferă un program de motivare prin intermediul burselor de excelență și/sau sociale conform reușitei.</p>
                
                <h2>III. VIAȚA SPIRITUALĂ ȘI ECLESIALĂ</h2>
                <p>Creșterea spirituală, slujirea efectivă și succesul academic sunt posibile prin petrecerea unui timp personal cu Dumnezeu. Astfel, se recomandă ca fiecare student să aibă un timp de devoțiune nu mai puțin de 15 minute pe zi în studierea Cuvântului lui Dumnezeu și în rugăciune.</p>
                <p>În timpul procesului de învățământ, studentul îmbină studiile cu viața eclesială. Studentul se implică în biserica locală, prin frecventare sistematică, participare activă, și/sau slujire. De asemenea, pe parcursul săptămânii se organizează capele și întâlniri separate pe grupe, care implică participarea studentului.</p>
                
                <h2>IV. CONDUITA ŞI ASPECTUL EXTERIOR</h2>
                <p><strong>Conduita studentului:</strong></p>
                <div class="list-item">a) este obligatoriu respectarea orarului stabilit; se admit absențe doar în baza unei cereri prealabile, aprobată de către decanul facultății sau pe motiv de boală, fapt adeverit printr-un certificat medical, eliberat de către o instituție medicală;</div>
                <div class="list-item">b) este interzisă convorbirea telefonică și/sau corespondența online în timpul orelor și a capelelor;</div>
                <div class="list-item">c) este interzisă prezența la ore a persoanelor care nu sunt studenți ai universității; orele de clasă pot fi frecventate doar de către studenții universității;</div>
                <div class="list-item">d) este interzisă păstrarea și întrebuințarea produselor alimentare în camerele de locuit și sălile de studii;</div>
                <div class="list-item">e) este interzis ca studentul să facă afirmații publice în numele UDG fără să fi fost împuternicit de către conducerea universității;</div>
                <div class="list-item">f) este interzisă ținerea animalelor de companie pe teritoriul universității;</div>
                <div class="list-item">g) este interzisă scoaterea veselei din cantina(ele) universității;</div>
                <div class="list-item">h) este interzis furtul, copiatul, minciuna, adulterul, fumatul, consumul de alcool și a substanțelor stupefiante, toxice și psihotrope, întrebuințarea limbajului vulgar/necenzurat, vizionarea și audierea materialelor cu conținut erotic și pornografic (prin rețele de socializare, site-uri, filme, reviste), și orice alte activități ce contravin principiilor biblice; </div>
                <div class="list-item">i) este interzisă aflarea băieților în camerele fetelor și viceversa, izolarea persoanelor de sex opus (sub orice pretext), relațiile intime (petting-ul, curvia);</div>
                <div class="list-item">j) este interzisă frecventarea barurilor și a altor localuri de noapte; </div>
                <div class="list-item">k) studentul universității DIVITIA GRATIAE trebuie să dea dovadă de un comportament cuviincios.</div>
            </div>
            
            
            
        </div>
    </div>

    <!-- Page 7 - Anexa 1 -->
    <div class="page page-break">
        <?php if ($secondHeader): ?>
            <img src="<?php echo $secondHeader; ?>" alt="University Header">
        <?php endif; ?>
        <div class="content">
            <div class="section">
                <p><strong>Conduita studentului:</strong></p>
                <div class="list-item">a) statutul de student al universității DIVITIA GRATIAE implică haine și coafură îngrijite, care corespund stilului de afaceri;</div>
                <div class="list-item">b) este inacceptabil să se poarte pantaloni scurți și îmbrăcăminte sport (în zilele de luni-vineri între orele 8.00–17.00);</div>
                <div class="list-item">c) fetele ar trebui să păstreze un simț al echilibrului în ceea ce privește machiajul;</div>
                <div class="list-item">d) este nepotrivit ca studentul să se prezinte în haine transparente și mulate, decolteu adânc, fuste mini;</div>
                <div class="list-item">e) studentul trebuie să își păstreze hainele curate și ordonate. </div>
                
                <h2>V. SOLUȚIONAREA CONFLICTELOR</h2>
                <p>Conflictele în care sunt implicați studenții, profesorii, sau personal administrativ sunt rezolvate în conformitate cu principiile biblice (Matei 18:15–17).</p>
                <p>Abordarea directă a conflictelor se va face cu persoana implicată într-o manieră respectoasă și constructivă. O astfel de comunicare permite ambelor părți să își exprime preocupările, să clarifice neînțelegerile și să identifice o cale de rezolvare.</p>
                <p>În cazul în care conflictul nu este soluționat, studentul se adresează dirigintelui/mentorului. Când conflictul are loc între diriginte/mentor și student, aceștia se adresează decanului facultății. Conflictele care nu pot fi soluționate la acest nivel sunt abordate/rezolvate în cadrul Consiliului universității.</p>
                <p>Răspândirea informațiilor care nu corespund cu realitățile interne ale universității, angajarea în bârfe sau formularea de plângeri care calomniază personalitatea cuiva, exprimarea destructivă în public a nemulțumirilor cu privire la normele și practicile în cadrul UDG, este strict interzisă.</p>
                <p>Prin aderarea la aceste principii și reguli, universitatea promovează un mediu respectuos și constructiv.</p>
                
                <h2>VI. VIAȚA DE STUDENT ȘI CĂSĂTORIA</h2>
                <p>Conducerea universității recomandă studenților necăsătoriți să se abțină de la căsătorie pe durata studiilor. Intenția din spatele acestei recomandări este de a ajuta studentul să se concentreze pe activitatea academică prin minimizarea provocărilor care pot apărea din cauza echilibrării studiilor cu responsabilitățile familiale. </p>
                <p>În cazul în care studentul decide să se căsătorească în timpul studiilor, UDG își rezervează dreptul să revizuiască Contractul de locațiune, iar studentul își asumă întreaga responsabilitate privind spațiul locativ și starea materială a familiei.</p>
                <p>Studenții care intenționează să se căsătorească, sunt încurajați să informeze administrația universității cu cel puțin 6 luni înainte de data planificată pentru nuntă.</p>
                
                <h2>VII. MUNCA ÎN FOLOSUL UNIVERSITĂȚII</h2>
                <p>Ca parte a angajamentului de sprijin financiar, universitatea acoperă în mod substanțial costurile studiilor și contează pe munca fizică a studenților în folosul universității. Este obligatoriu pentru fiecare student să fie de serviciu la cantina universității, să participe în lucrări de sanitație în zilele sanitare, și să mențină ordinea și curățenia în clasa și încăperile în care acesta își desfășoară studii (conform graficului stabilit).</p>
                <p>Studenților cărora li se oferă loc de cămin în unul din căminele universității, trebuie să contribuie la buna menținere a ordinii și curățeniei căminelor și spațiilor adiacente acestora (conform regulamentului de funcționare a căminelor UDG și fișei de sector), lucrând 80 de ore astronomice pe semestru. Pentru a îndeplini această cerință, studentului care solicită un loc în căminele universității i se va atribui aleatoriu un sector de muncă (în incinta căminului și/sau teritoriului adiacent) la începutul anului de studii (activitate desemnată și coordonată de către administrația căminului).</p>
                <p>Studentul are dreptul să pledeze (prin depunerea cererii adiționale) să fie scutit de muncă în folosul universității/căminului, având motive întemeiate. În cazul dacă cererea lui va fi aprobată, contribuția lui în muncă poate fi recalculată în cuantumul financiar cu revizuirea plăților de studii/locațiune. Valoarea unei ore de muncă este stabilită la 40 MDL, rezultând la o contribuție totală de 6400 MDL pentru sector pe parcurs la un an de studii.</p>
                <p style="text-align: center; margin:30px;"><em>După ce studentul a luat cunoștință cu normele respective, acesta semnează contractul de studii, asumându-și responsabilitatea respectării normelor sus-menționate.</em></p>
            </div>
        </div>
    </div>
    


    
    

</body>
</html>