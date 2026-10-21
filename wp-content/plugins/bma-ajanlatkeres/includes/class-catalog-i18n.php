<?php
/**
 * RO / EN texts of the v2 service catalog (see BMA_AJ_Catalog).
 *
 * sync() writes them where the frontend reads translations: Polylang
 * translations of the service posts, and the bma_i18n_overlay option for
 * items, add-ons and fields. Translated service posts are only created when
 * missing, so later edits in wp-admin stay.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Catalog_I18n {

	public const LANGS = array( 'ro', 'en' );

	/**
	 * Service texts by slug and language.
	 *
	 * @return array<string, array<string, array<string, string>>>
	 */
	public static function services(): array {
		return array(
			'epuletgepeszet' => array(
				'ro' => array(
					'slug'           => 'proiectare-instalatii',
					'title'          => 'Proiectare instalații în construcții',
					'short'          => 'Instalații',
					'excerpt'        => 'Proiectarea sistemelor din interiorul clădirii: apă-canal, încălzire, ventilație și climatizare, rețea electrică, gaz interior.',
					'content'        => '<p>Proiectăm instalațiile clădirii, de la proiectul pentru autorizare până la proiectul de execuție. Într-o singură cerere puteți solicita mai multe sisteme, iar faza de proiectare o alegeți o singură dată.</p>',
					'choose_summary' => 'Alegeți acest serviciu dacă aveți nevoie de proiect pentru instalațiile de apă, încălzire, ventilație, electrice sau gaz ale clădirii.',
					'when_needed'    => "la construirea unei clădiri noi, rezidențiale sau comerciale\nla renovarea, extinderea sau schimbarea destinației unei clădiri existente\ndacă autorizația de construire sau execuția necesită proiect de instalații",
					'needs_required' => "suprafața utilă și numărul de niveluri\nplanurile de arhitectură (DWG sau PDF) – fără ele nu putem face o ofertă relevantă\nla încălzire: sursa de căldură planificată (gaz, pompă de căldură, termoficare, lemn sau peleți)\nla gaz interior: dacă există deja branșament de gaz",
					'needs_optional' => "certificatul de urbanism\nproiecte existente",
					'needs_later'    => '',
				),
				'en' => array(
					'slug'           => 'building-services-design',
					'title'          => 'Building services design',
					'short'          => 'Building services',
					'excerpt'        => 'Design of the systems inside the building: water and drainage, heating, ventilation and air conditioning, electrical network, indoor gas.',
					'content'        => '<p>We design the building services, from the permit design to the construction design. You can request several systems in one request, and you choose the design phase only once.</p>',
					'choose_summary' => 'Choose this service if the building needs a design for its water, heating, ventilation, electrical or gas systems.',
					'when_needed'    => "when building a new residential or commercial building\nwhen renovating, extending or changing the use of an existing building\nif the building permit or the construction needs a building services design",
					'needs_required' => "usable floor area and number of storeys\narchitectural floor plans (DWG or PDF) – without them we cannot give a meaningful quote\nfor heating: the planned heat source (gas, heat pump, district heating, wood or pellets)\nfor indoor gas: whether there is already a gas connection",
					'needs_optional' => "urban planning certificate\nexisting designs",
					'needs_later'    => '',
				),
			),
			'tuzvedelem'     => array(
				'ro' => array(
					'slug'           => 'securitate-la-incendiu',
					'title'          => 'Proiectare sisteme de securitate la incendiu',
					'short'          => 'Securitate la incendiu',
					'excerpt'        => 'Alimentare cu apă pentru stingere, sprinklere, evacuarea fumului și a căldurii, detecție și semnalizare incendiu, până la avizul ISU.',
					'content'        => '<p>Proiectăm sistemele care, în caz de incendiu, semnalizează, sting și evacuează fumul. Vă ajutăm să stabiliți ce este obligatoriu pentru clădirea dumneavoastră și, la cerere, ne ocupăm și de avizul ISU.</p>',
					'choose_summary' => 'Alegeți acest serviciu dacă clădirea are nevoie de sisteme de securitate la incendiu sau de aviz ISU.',
					'when_needed'    => "la clădiri publice, comerciale, industriale sau rezidențiale cu mai multe niveluri\ndacă autoritatea sau certificatul de urbanism cere aviz de securitate la incendiu\nla schimbarea destinației sau la extindere",
					'needs_required' => "suprafața, înălțimea și destinația clădirii\nplanurile",
					'needs_optional' => 'scenariul de securitate la incendiu (dacă nu îl aveți, îl obținem)',
					'needs_later'    => '',
				),
				'en' => array(
					'slug'           => 'fire-protection-design',
					'title'          => 'Fire protection systems design',
					'short'          => 'Fire protection',
					'excerpt'        => 'Firefighting water supply, sprinklers, smoke and heat exhaust, fire detection and alarm, up to the fire safety permit (ISU).',
					'content'        => '<p>We design the systems that detect, extinguish and exhaust smoke in case of fire. We help you decide what is mandatory for your building and, if you wish, we also handle the fire safety permit (ISU).</p>',
					'choose_summary' => 'Choose this service if the building needs fire protection systems or a fire safety permit (ISU).',
					'when_needed'    => "for public, commercial, industrial or multi-storey residential buildings\nif the authority or the urban planning certificate requires a fire safety permit\nwhen changing the use of a building or extending it",
					'needs_required' => "floor area, building height and use\nfloor plans",
					'needs_optional' => 'fire safety scenario (if you don\'t have one, we obtain it)',
					'needs_later'    => '',
				),
			),
			'kozmuhalozatok' => array(
				'ro' => array(
					'slug'           => 'retele-de-utilitati',
					'title'          => 'Proiectare rețele de utilități',
					'short'          => 'Rețele de utilități',
					'excerpt'        => 'Rețele pe teren și în localitate: apă, canalizare menajeră, ape pluviale, apă pentru incendiu, tratare și stații de pompare.',
					'content'        => '<p>Proiectăm rețelele și instalațiile din exteriorul clădirilor: conducte pe teren, rețele stradale și ale localității, tratarea apei și a apelor uzate, stații de pompare.</p>',
					'choose_summary' => 'Alegeți acest serviciu dacă aveți nevoie de rețea de apă, canalizare, ape pluviale sau apă pentru incendiu pe teren sau în localitate.',
					'when_needed'    => "la echiparea cu utilități a unui ansamblu rezidențial, parc industrial sau amplasament\nla construirea sau extinderea rețelelor stradale sau ale localității (investiții ale primăriilor)\ndacă este nevoie de tratarea proprie a apei sau a apelor uzate",
					'needs_required' => "lungimea estimată a rețelei\nplan de situație sau traseu\nnumărul de consumatori deserviți sau de locuitori echivalenți",
					'needs_optional' => "punctul de racord, dacă este cunoscut\nridicare topografică\nstudiu geotehnic",
					'needs_later'    => 'Ce lipsește, obținem noi – costul apare în ofertă ca poziție separată.',
				),
				'en' => array(
					'slug'           => 'utility-network-design',
					'title'          => 'Utility network design',
					'short'          => 'Utility networks',
					'excerpt'        => 'Networks on site and across the settlement: water, sewage, stormwater, firefighting water, treatment and pumping stations.',
					'content'        => '<p>We design the networks and facilities outside the buildings: pipes on the plot, street and settlement networks, water and wastewater treatment, pumping stations.</p>',
					'choose_summary' => 'Choose this service if you need a water, sewage, stormwater or firefighting water network on a plot or across a settlement.',
					'when_needed'    => "when servicing a residential estate, industrial park or site with utilities\nwhen building or extending street or settlement networks (municipal investments)\nif your own water or wastewater treatment is needed",
					'needs_required' => "estimated network length\nsite plan or route\nnumber of consumers served or population equivalent",
					'needs_optional' => "the connection point, if known\ntopographic survey\ngeotechnical study",
					'needs_later'    => 'Whatever is missing, we obtain – the cost appears as a separate line in the quote.',
				),
			),
			'bekotesek'      => array(
				'ro' => array(
					'slug'           => 'bransamente',
					'title'          => 'Branșamente și racorduri',
					'short'          => 'Branșamente',
					'excerpt'        => 'Racordarea clădirii la rețelele de utilități: apă, canalizare menajeră, ape pluviale și apă pentru incendiu.',
					'content'        => '<p>Realizăm proiectele de branșament prin care clădirea se poate racorda la rețelele de utilități. Pentru branșamente se întocmește o singură documentație, aici nu trebuie să alegeți faza de proiectare.</p><p>Pentru proiectarea branșamentului este nevoie de certificat de urbanism. Dacă nu îl aveți încă, îl obținem noi.</p>',
					'choose_summary' => 'Alegeți acest serviciu dacă clădirea trebuie racordată la rețeaua de apă, canalizare, ape pluviale sau apă pentru incendiu.',
					'when_needed'    => "la o clădire nouă, încă neracordată la utilități\ndacă branșamentul existent este insuficient sau trebuie mutat",
					'needs_required' => "plan de situație cu terenul și clădirea\nconsumul estimat sau tipul clădirii racordate",
					'needs_optional' => 'amplasarea rețelei existente și numele furnizorului (dacă nu le știți, le obținem)',
					'needs_later'    => '',
				),
				'en' => array(
					'slug'           => 'utility-connections',
					'title'          => 'Utility connections',
					'short'          => 'Connections',
					'excerpt'        => 'Connecting the building to the utility networks: water, sewage, stormwater and firefighting water.',
					'content'        => '<p>We prepare the connection designs that let the building connect to the utility networks. Connections need a single set of documents, so you don\'t choose a design phase here.</p><p>A connection design needs an urban planning certificate. If you don\'t have one yet, we obtain it.</p>',
					'choose_summary' => 'Choose this service if the building has to be connected to the water, sewage, stormwater or firefighting water network.',
					'when_needed'    => "for a new building that is not yet connected to the utilities\nif the existing connection is too small or has to be moved",
					'needs_required' => "site plan showing the plot and the building\nexpected consumption or the type of building connected",
					'needs_optional' => 'location of the existing network and the provider\'s name (if you don\'t know them, we obtain them)',
					'needs_later'    => '',
				),
			),
		);
	}

	/**
	 * Items, permit add-ons (engedelyeztetes-<service>) and general add-ons by
	 * slug: lang => [name, short description, choose if].
	 *
	 * @return array<string, array<string, string[]>>
	 */
	public static function items(): array {
		return array(
			// Épületgépészet.
			'viz-csatorna'                        => array(
				'ro' => array( 'Apă-canal', 'Alimentare cu apă interioară, evacuarea apelor uzate și pluviale', 'în clădire se realizează sau se modifică puncte de consum de apă, băi, bucătărie.' ),
				'en' => array( 'Water and drainage', 'Indoor water supply, wastewater and stormwater drainage', 'water outlets, bathrooms or a kitchen are being built or changed in the building.' ),
			),
			'futes'                               => array(
				'ro' => array( 'Încălzire', 'Sursă de căldură, centrală termică, sistem de încălzire', 'se realizează un sistem de încălzire nou sau îl înlocuiți, extindeți pe cel existent.' ),
				'en' => array( 'Heating', 'Heat source, boiler room, heating system', 'a new heating system is being built, or you are replacing or extending the existing one.' ),
			),
			'szellozes-klima'                     => array(
				'ro' => array( 'Ventilație, climatizare și răcire', 'Ventilație, climatizare, sisteme de răcire', 'clădirea primește sistem de ventilație, climatizare sau răcire, sau acesta este impus de norme.' ),
				'en' => array( 'Ventilation, air conditioning and cooling', 'Ventilation, air conditioning, cooling systems', 'a ventilation, air conditioning or cooling system goes into the building, or regulations require one.' ),
			),
			'elektromos-halozat'                  => array(
				'ro' => array( 'Rețea electrică', 'Curenți tari, curenți slabi, iluminat', 'se realizează o rețea electrică nouă sau o modificați, extindeți pe cea existentă.' ),
				'en' => array( 'Electrical network', 'Power, low-voltage systems, lighting', 'a new electrical network is being built, or you are changing or extending the existing one.' ),
			),
			'belso-gazhalozat'                    => array(
				'ro' => array( 'Instalație interioară de gaz', 'Alimentare cu gaz în interiorul clădirii', 'racordați aparate pe gaz (centrală, aragaz) sau se modifică instalația interioară de gaz.' ),
				'en' => array( 'Indoor gas installation', 'Gas supply inside the building', 'you are connecting gas appliances (boiler, cooker) or the indoor gas pipework changes.' ),
			),
			// Tűzvédelem.
			'tuzolto-vizellatas'                  => array(
				'ro' => array( 'Alimentare cu apă pentru stingerea incendiilor', 'Hidranți interiori și exteriori, rezervor de incendiu, pompe de incendiu', 'clădirea sau terenul are nevoie de hidranți sau de rezervor de apă pentru incendiu.' ),
				'en' => array( 'Firefighting water supply', 'Indoor and outdoor hydrants, fire water tank, fire pumps', 'the building or the plot needs hydrants or a fire water tank.' ),
			),
			'sprinkler'                           => array(
				'ro' => array( 'Sprinklere / instalație automată de stingere', 'Instalații automate de stingere', 'este vorba de o clădire cu suprafață mare sau cu destinație cu risc de incendiu.' ),
				'en' => array( 'Sprinklers / automatic extinguishing system', 'Automatic extinguishing systems', 'the building has a large floor area or a use with a high fire risk.' ),
			),
			'ho-es-fustelvezetes'                 => array(
				'ro' => array( 'Evacuarea fumului și a căldurii', 'Desfumare naturală și mecanică', 'casele de scară, coridoarele sau spațiile mari trebuie desfumate.' ),
				'en' => array( 'Smoke and heat exhaust', 'Natural and mechanical smoke exhaust', 'staircases, corridors or large spaces need smoke clearance.' ),
			),
			'tuzjelzo-rendszer'                   => array(
				'ro' => array( 'Sistem de detecție și semnalizare a incendiilor', 'Detectarea și semnalizarea incendiului', 'detecția automată a incendiului este obligatorie sau de dorit în clădire.' ),
				'en' => array( 'Fire detection and alarm system', 'Fire detection and alarm', 'automatic fire detection is mandatory or desirable in the building.' ),
			),
			// Közműhálózatok.
			'vizhalozat'                          => array(
				'ro' => array( 'Rețea de apă', 'Rețea de alimentare cu apă potabilă', 'mai multe clădiri, o stradă sau o zonă a localității trebuie alimentate cu apă.' ),
				'en' => array( 'Water network', 'Drinking water supply network', 'several buildings, a street or part of a settlement need water supply.' ),
			),
			'viztisztitas-vizkezeles'             => array(
				'ro' => array( 'Tratarea și epurarea apei', 'Tratarea apei potabile, echipamente de tratare, stații de tratare', 'apa din sursă (de ex. dintr-un puț) trebuie tratată sau filtrată.' ),
				'en' => array( 'Water treatment', 'Drinking water treatment, treatment equipment, treatment plants', 'water from the source (e.g. a well) has to be treated or purified.' ),
			),
			'szennyvizhalozat'                    => array(
				'ro' => array( 'Rețea de canalizare menajeră', 'Rețea de colectare a apelor uzate', 'evacuarea apelor uzate trebuie rezolvată pentru mai multe clădiri sau străzi.' ),
				'en' => array( 'Sewage network', 'Wastewater collection network', 'sewage has to be drained from several buildings or streets.' ),
			),
			'csapadekviz-halozat'                 => array(
				'ro' => array( 'Rețea de ape pluviale', 'Evacuarea și colectarea apelor pluviale', 'apa de ploaie de pe drumuri, parcări sau acoperișuri trebuie evacuată sau colectată.' ),
				'en' => array( 'Stormwater network', 'Stormwater drainage and collection', 'rainwater from roads, car parks or roofs has to be drained or collected.' ),
			),
			'tuzivizhalozat'                      => array(
				'ro' => array( 'Rețea de apă pentru incendiu', 'Rețea exterioară de alimentare cu apă pentru incendiu', 'zona are nevoie de hidranți exteriori.' ),
				'en' => array( 'Firefighting water network', 'Outdoor firefighting water network', 'the area needs outdoor hydrants.' ),
			),
			'atemelok-szivattyutelepek'           => array(
				'ro' => array( 'Stații de pompare', 'Stații de pompare ape uzate, stații de ridicare a presiunii', 'din cauza terenului apa uzată nu se scurge gravitațional sau presiunea apei este insuficientă.' ),
				'en' => array( 'Pumping stations', 'Sewage pumping stations, booster stations', 'because of the terrain sewage cannot flow by gravity, or the water pressure is too low.' ),
			),
			'szennyviztisztitas'                  => array(
				'ro' => array( 'Epurarea și preepurarea apelor uzate', 'Stații de epurare, separatoare de grăsimi și hidrocarburi', 'nu există canalizare publică sau apa uzată necesită preepurare (de ex. bucătărie, parcare).' ),
				'en' => array( 'Wastewater treatment and pre-treatment', 'Treatment plants, grease and oil separators', 'there is no public sewer, or the wastewater needs pre-treatment (e.g. a kitchen or car park).' ),
			),
			// Bekötések.
			'vizbekotes'                          => array(
				'ro' => array( 'Branșament apă', 'Racordare la rețeaua publică de apă', 'aveți nevoie de un branșament nou de apă sau îl extindeți, mutați pe cel existent.' ),
				'en' => array( 'Water connection', 'Connection to the public water network', 'you need a new water connection, or are extending or moving the existing one.' ),
			),
			'szennyvizbekotes'                    => array(
				'ro' => array( 'Racord canalizare', 'Racordare la rețeaua publică de canalizare', 'clădirea trebuie racordată la canalizare.' ),
				'en' => array( 'Sewer connection', 'Connection to the public sewer network', 'the building has to be connected to the sewer.' ),
			),
			'csapadekviz-bekotes'                 => array(
				'ro' => array( 'Racord ape pluviale', 'Racordare la rețeaua de ape pluviale', 'apa de ploaie de pe teren trebuie dirijată în rețeaua de ape pluviale.' ),
				'en' => array( 'Stormwater connection', 'Connection to the stormwater network', 'rainwater from the plot has to be led into the stormwater network.' ),
			),
			'tuzivizbekotes'                      => array(
				'ro' => array( 'Branșament apă pentru incendiu', 'Racordare la rețeaua de apă pentru incendiu', 'clădirea va avea hidranți sau instalație de stingere.' ),
				'en' => array( 'Firefighting water connection', 'Connection to the firefighting water network', 'the building will have hydrants or an extinguishing system.' ),
			),
			// Permits.
			'engedelyeztetes-epuletgepeszet'      => array(
				'ro' => array( 'Obținere avize și autorizații – Instalații', 'Avizele necesare sunt enumerate în certificatul de urbanism, le precizăm în ofertă.', '' ),
				'en' => array( 'Permitting – Building services', 'The permits needed are listed in the urban planning certificate; we specify them in the quote.', '' ),
			),
			'engedelyeztetes-tuzvedelem'          => array(
				'ro' => array( 'Obținere avize – Securitate la incendiu (ISU)', 'Avizul de securitate la incendiu (ISU). Avizele necesare sunt enumerate în certificatul de urbanism, le precizăm în ofertă.', '' ),
				'en' => array( 'Permitting – Fire protection (ISU)', 'Fire safety permit (ISU). The permits needed are listed in the urban planning certificate; we specify them in the quote.', '' ),
			),
			'engedelyeztetes-kozmuhalozatok'      => array(
				'ro' => array( 'Obținere avize și autorizații – Rețele de utilități', 'Avizele necesare sunt enumerate în certificatul de urbanism, le precizăm în ofertă.', '' ),
				'en' => array( 'Permitting – Utility networks', 'The permits needed are listed in the urban planning certificate; we specify them in the quote.', '' ),
			),
			'engedelyeztetes-bekotesek'           => array(
				'ro' => array( 'Obținere avize și autorizații – Branșamente', 'Avizele necesare sunt enumerate în certificatul de urbanism, le precizăm în ofertă.', '' ),
				'en' => array( 'Permitting – Connections', 'The permits needed are listed in the urban planning certificate; we specify them in the quote.', '' ),
			),
			// General add-ons.
			'urbanisztikai-bizonylat-beszerzese'  => array(
				'ro' => array( 'Obținerea certificatului de urbanism', 'Documentul emis de primărie, care enumeră avizele și autorizațiile necesare.', '' ),
				'en' => array( 'Obtaining the urban planning certificate', 'The document issued by the municipality (certificat de urbanism) that lists the permits needed.', '' ),
			),
			'tuzvedelmi-forgatokonyv-beszerzese'  => array(
				'ro' => array( 'Obținerea scenariului de securitate la incendiu', 'Baza proiectării sistemelor de securitate la incendiu.', '' ),
				'en' => array( 'Obtaining the fire safety scenario', 'The basis of fire protection design (scenariu de securitate la incendiu).', '' ),
			),
			'topografiai-felmeres-beszerzese'     => array(
				'ro' => array( 'Obținerea ridicării topografice', 'Măsurarea exactă a terenului, pentru traseul rețelelor.', '' ),
				'en' => array( 'Obtaining a topographic survey', 'An exact survey of the plot and terrain, for the network routes.', '' ),
			),
			'geotechnikai-tanulmany-beszerzese'   => array(
				'ro' => array( 'Obținerea studiului geotehnic', 'Investigarea terenului, pentru proiectarea conductelor și a construcțiilor aferente.', '' ),
				'en' => array( 'Obtaining a geotechnical study', 'Soil investigation for the design of pipes and structures.', '' ),
			),
			'kozmuszolgaltatoi-adatok-beszerzese' => array(
				'ro' => array( 'Obținerea datelor de la furnizorii de utilități', 'Amplasarea rețelei existente și punctul de racord, de la furnizor.', '' ),
				'en' => array( 'Obtaining utility provider data', 'The location of the existing network and the connection point, from the provider.', '' ),
			),
			'terv-ellenorzes'                     => array(
				'ro' => array( 'Verificarea proiectului de un verificator atestat', 'Proiectele de execuție trebuie verificate de un verificator de proiecte atestat. Obligatoriu pentru proiectele de execuție.', '' ),
				'en' => array( 'Design check by a certified verifier', 'Construction designs must be checked by a certified design verifier (verificator). Mandatory for construction designs.', '' ),
			),
			'tervezoi-muvezetes'                  => array(
				'ro' => array( 'Asistență tehnică din partea proiectantului pe durata execuției', 'Proiectantul verifică pe durata execuției că se construiește conform proiectului.', '' ),
				'en' => array( 'Designer\'s supervision during construction', 'The designer checks during construction that the work follows the design.', '' ),
			),
			'helyszini-felmeres'                  => array(
				'ro' => array( 'Releveu la fața locului', 'Venim la fața locului și evaluăm starea existentă.', '' ),
				'en' => array( 'On-site survey', 'We visit the site and survey the existing state.', '' ),
			),
			'muszaki-szakvelemeny'                => array(
				'ro' => array( 'Expertiză tehnică, evaluarea unui sistem existent', 'Opinie scrisă despre starea unui sistem existent și ce trebuie făcut.', '' ),
				'en' => array( 'Technical expert opinion, survey of an existing system', 'A written opinion on the state of an existing system and what needs doing.', '' ),
			),
			'tanacsadas'                          => array(
				'ro' => array( 'Nu știu exact de ce am nevoie – solicit consultanță', 'Discutăm împreună proiectul și vă recomandăm de ce aveți nevoie.', '' ),
				'en' => array( 'I\'m not sure what I need – I\'d like advice', 'We go through the project together and recommend what you need.', '' ),
			),
			'egyedi-keres'                        => array(
				'ro' => array( 'Altă solicitare', 'Descrieți de ce ați mai avea nevoie.', '' ),
				'en' => array( 'Other request', 'Describe what else you need.', '' ),
			),
		);
	}

	/**
	 * Field texts by key: lang => [label, description, placeholder, option labels].
	 * Option labels follow the order of BMA_AJ_Catalog::fields() options.
	 *
	 * @return array<string, array<string, array{0: string, 1: string, 2: string, 3: string[]}>>
	 */
	public static function fields(): array {
		$yes_no = array(
			'ro' => array( 'Da', 'Nu' ),
			'en' => array( 'Yes', 'No' ),
		);
		return array(
			'telepules'                    => array(
				'ro' => array( 'Localitate', '', 'ex. Odorheiu Secuiesc', array() ),
				'en' => array( 'Town or village', '', 'e.g. Odorheiu Secuiesc', array() ),
			),
			'cim_vagy_hrsz'                => array(
				'ro' => array( 'Adresă sau număr de carte funciară', 'Este suficientă una dintre ele.', 'ex. Str. Kossuth nr. 12, sau CF 12345', array() ),
				'en' => array( 'Address or land registry number', 'One of the two is enough.', 'e.g. 12 Kossuth St., or CF 12345', array() ),
			),
			'epulet_funkcio'               => array(
				'ro' => array( 'Destinația clădirii sau a construcției', '', '', array( 'Locuință', 'Clădire de locuințe colective', 'Birouri', 'Comercial', 'Industrial, depozit', 'Clădire publică', 'Construcție de utilități', 'Altele' ) ),
				'en' => array( 'Use of the building or facility', '', '', array( 'House', 'Apartment building', 'Office', 'Retail', 'Industrial, warehouse', 'Public building', 'Utility facility', 'Other' ) ),
			),
			'projekt_jellege'              => array(
				'ro' => array( 'Tipul proiectului', '', '', array( 'Construcție nouă', 'Renovare', 'Extindere' ) ),
				'en' => array( 'Type of project', '', '', array( 'New build', 'Renovation', 'Extension' ) ),
			),
			'alapterulet'                  => array(
				'ro' => array( 'Suprafață utilă (m²)', 'Dacă nu o știți exact, estimați-o.', 'ex. 160', array() ),
				'en' => array( 'Usable floor area (m²)', 'If you don\'t know it exactly, estimate it.', 'e.g. 160', array() ),
			),
			'szintek_szama'                => array(
				'ro' => array( 'Număr de niveluri', 'Subsol, parter și etaje împreună.', 'ex. 2', array() ),
				'en' => array( 'Number of storeys', 'Basement, ground floor and upper floors together.', 'e.g. 2', array() ),
			),
			'hatarido'                     => array(
				'ro' => array( 'Termen estimat', 'Până când aveți nevoie de proiecte?', '', array() ),
				'en' => array( 'Expected deadline', 'When do you need the designs?', '', array() ),
			),
			'urbanisztikai_bizonylat'      => array(
				'ro' => array( 'Aveți certificat de urbanism?', 'Dacă nu aveți, nu este o problemă: adăugăm obținerea lui la serviciile suplimentare.', '', array( 'Da', 'Nu', 'În curs' ) ),
				'en' => array( 'Do you have an urban planning certificate?', 'If not, that\'s fine: we add obtaining it to the add-ons.', '', array( 'Yes', 'No', 'In progress' ) ),
			),
			'urbanisztikai_bizonylat_fajl' => array(
				'ro' => array( 'Încărcați certificatul de urbanism', '', '', array() ),
				'en' => array( 'Upload the urban planning certificate', '', '', array() ),
			),
			'meglevo_dokumentumok'         => array(
				'ro' => array( 'Proiecte existente, alte documente', '', '', array() ),
				'en' => array( 'Existing designs, other documents', '', '', array() ),
			),
			'alaprajz'                     => array(
				'ro' => array( 'Planuri de arhitectură (DWG sau PDF)', 'Fără ele nu putem face o ofertă relevantă.', '', array() ),
				'en' => array( 'Architectural floor plans (DWG or PDF)', 'Without them we cannot give a meaningful quote.', '', array() ),
			),
			'hoforras'                     => array(
				'ro' => array( 'Sursa de căldură planificată', '', '', array( 'Gaz', 'Pompă de căldură', 'Termoficare', 'Lemn sau peleți', 'Încă nu știu' ) ),
				'en' => array( 'Planned heat source', '', '', array( 'Gas', 'Heat pump', 'District heating', 'Wood or pellets', 'Not sure yet' ) ),
			),
			'gazbekotes_megvan'            => array(
				'ro' => array( 'Există deja branșament de gaz?', '', '', array( 'Da', 'Nu', 'Nu știu' ) ),
				'en' => array( 'Is there already a gas connection?', '', '', array( 'Yes', 'No', 'I don\'t know' ) ),
			),
			'epuletmagassag'               => array(
				'ro' => array( 'Înălțimea clădirii (m)', '', 'ex. 12', array() ),
				'en' => array( 'Building height (m)', '', 'e.g. 12', array() ),
			),
			'tuzvedelmi_forgatokonyv'      => array(
				'ro' => array( 'Există scenariu de securitate la incendiu?', '', '', $yes_no['ro'] ),
				'en' => array( 'Is there a fire safety scenario?', '', '', $yes_no['en'] ),
			),
			'tuzvedelmi_forgatokonyv_fajl' => array(
				'ro' => array( 'Încărcați scenariul de securitate la incendiu', '', '', array() ),
				'en' => array( 'Upload the fire safety scenario', '', '', array() ),
			),
			'halozat_hossz'                => array(
				'ro' => array( 'Lungimea estimată a rețelei (m)', '', 'ex. 850', array() ),
				'en' => array( 'Estimated network length (m)', '', 'e.g. 850', array() ),
			),
			'nyomvonal'                    => array(
				'ro' => array( 'Plan de situație sau descrierea traseului', 'Dacă aveți plan de situație, încărcați-l mai jos.', 'ex. de-a lungul străzii Principale, de la nr. 12 până la pârâu', array() ),
				'en' => array( 'Site plan or description of the route', 'If you have a site plan, upload it below.', 'e.g. along Main Street, from no. 12 to the stream', array() ),
			),
			'nyomvonal_fajl'               => array(
				'ro' => array( 'Plan de situație sau plan de traseu', '', '', array() ),
				'en' => array( 'Site plan or route drawing', '', '', array() ),
			),
			'fogyasztok_szama'             => array(
				'ro' => array( 'Număr de consumatori deserviți sau de locuitori echivalenți', '', 'ex. 120', array() ),
				'en' => array( 'Number of consumers served or population equivalent', '', 'e.g. 120', array() ),
			),
			'csatlakozasi_pont_ismert'     => array(
				'ro' => array( 'Se cunoaște punctul de racord?', '', '', $yes_no['ro'] ),
				'en' => array( 'Is the connection point known?', '', '', $yes_no['en'] ),
			),
			'topografiai_felmeres'         => array(
				'ro' => array( 'Există ridicare topografică?', '', '', $yes_no['ro'] ),
				'en' => array( 'Is there a topographic survey?', '', '', $yes_no['en'] ),
			),
			'topografiai_felmeres_fajl'    => array(
				'ro' => array( 'Încărcați ridicarea topografică', '', '', array() ),
				'en' => array( 'Upload the topographic survey', '', '', array() ),
			),
			'geotechnikai_tanulmany'       => array(
				'ro' => array( 'Există studiu geotehnic?', '', '', $yes_no['ro'] ),
				'en' => array( 'Is there a geotechnical study?', '', '', $yes_no['en'] ),
			),
			'geotechnikai_tanulmany_fajl'  => array(
				'ro' => array( 'Încărcați studiul geotehnic', '', '', array() ),
				'en' => array( 'Upload the geotechnical study', '', '', array() ),
			),
			'helyszinrajz_fajl'            => array(
				'ro' => array( 'Plan de situație cu terenul și clădirea', '', '', array() ),
				'en' => array( 'Site plan showing the plot and the building', '', '', array() ),
			),
			'varhato_fogyasztas'           => array(
				'ro' => array( 'Consumul estimat sau tipul clădirii racordate', '', 'ex. casă unifamilială, 4 persoane', array() ),
				'en' => array( 'Expected consumption or type of building connected', '', 'e.g. family house, 4 people', array() ),
			),
			'meglevo_halozat_ismert'       => array(
				'ro' => array( 'Se cunosc amplasarea rețelei existente și furnizorul?', '', '', $yes_no['ro'] ),
				'en' => array( 'Do you know where the existing network is and who the provider is?', '', '', $yes_no['en'] ),
			),
			'szolgaltato_neve'             => array(
				'ro' => array( 'Numele furnizorului', '', 'ex. Aquaserv', array() ),
				'en' => array( 'Provider name', '', 'e.g. Aquaserv', array() ),
			),
			'egyedi_keres_leiras'          => array(
				'ro' => array( 'Descrieți de ce ați mai avea nevoie', '', '', array() ),
				'en' => array( 'Describe what else you need', '', '', array() ),
			),
		);
	}

	/**
	 * Write the translations (called from BMA_AJ_Catalog::sync()).
	 *
	 * @param array<string, int> $service_ids Service slug => HU post ID.
	 */
	public static function sync( array $service_ids ): void {
		global $wpdb;

		$overlay = get_option( 'bma_i18n_overlay', array() );
		$overlay = is_array( $overlay ) ? $overlay : array();

		$items = $wpdb->prefix . 'bma_service_items';
		foreach ( self::items() as $slug => $langs ) {
			$ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$items} WHERE slug = %s AND is_active = 1", $slug ) );
			foreach ( $ids as $id ) {
				foreach ( $langs as $lang => $t ) {
					$overlay['service_items'][ (string) $id ][ $lang ] = array(
						'name'              => $t[0],
						'short_description' => $t[1],
						'choose_if'         => $t[2],
					);
				}
			}
		}

		$fields = $wpdb->prefix . 'bma_dynamic_fields';
		foreach ( self::fields() as $key => $langs ) {
			$id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$fields} WHERE field_key = %s", $key ) );
			if ( ! $id ) {
				continue;
			}
			foreach ( $langs as $lang => $t ) {
				$overlay['dynamic_fields'][ (string) $id ][ $lang ] = array(
					'client_label' => $t[0],
					'description'  => $t[1],
					'placeholder'  => $t[2],
					'options'      => $t[3],
				);
			}
		}

		// The first RO/EN e-mail translations left a placeholder where the HU
		// template prints the company phone and e-mail.
		foreach ( (array) ( $overlay['email_templates'] ?? array() ) as $id => $langs ) {
			foreach ( (array) $langs as $lang => $tpl ) {
				if ( isset( $tpl['body'] ) && is_string( $tpl['body'] ) ) {
					$overlay['email_templates'][ $id ][ $lang ]['body'] = str_replace(
						array( '[DATE DE CONTACT]', '[CONTACT DETAILS]' ),
						"{company_phone}\n{company_email}",
						$tpl['body']
					);
				}
			}
		}

		update_option( 'bma_i18n_overlay', $overlay );

		if ( function_exists( 'pll_save_post_translations' ) ) {
			self::sync_service_posts( $service_ids );
		}
	}

	/**
	 * Create missing RO/EN service posts and keep their operational meta in step.
	 *
	 * @param array<string, int> $service_ids Service slug => HU post ID.
	 */
	private static function sync_service_posts( array $service_ids ): void {
		$languages = function_exists( 'pll_languages_list' ) ? (array) pll_languages_list() : array();
		$texts     = self::services();
		$tr_ids    = array();

		foreach ( $service_ids as $slug => $source_id ) {
			$translations = pll_get_post_translations( $source_id );
			$translations['hu'] = $source_id;

			foreach ( self::LANGS as $lang ) {
				if ( ! in_array( $lang, $languages, true ) || empty( $texts[ $slug ][ $lang ] ) ) {
					continue;
				}
				$t  = $texts[ $slug ][ $lang ];
				$id = (int) ( $translations[ $lang ] ?? 0 );

				if ( ! $id ) {
					$id = (int) wp_insert_post(
						array(
							'post_type'    => BMA_AJ_Service_CPT::POST_TYPE,
							'post_status'  => 'publish',
							'post_title'   => $t['title'],
							'post_name'    => $t['slug'],
							'post_excerpt' => $t['excerpt'],
							'post_content' => $t['content'],
						)
					);
					if ( ! $id ) {
						continue;
					}
					pll_set_post_language( $id, $lang );
					// Texts are only seeded on creation; later edits in wp-admin win.
					update_post_meta( $id, '_bma_short_description', $t['excerpt'] );
					update_post_meta( $id, '_bma_short_title', $t['short'] );
					update_post_meta( $id, '_bma_choose_summary', $t['choose_summary'] );
					update_post_meta( $id, '_bma_when_needed', $t['when_needed'] );
					update_post_meta( $id, '_bma_needs_required', $t['needs_required'] );
					update_post_meta( $id, '_bma_needs_optional', $t['needs_optional'] );
					update_post_meta( $id, '_bma_needs_later', $t['needs_later'] );
					update_post_meta( $id, '_bma_use_placeholder', '0' );
				}

				foreach ( array( '_bma_is_active', '_bma_sort_order', '_bma_no_design_phase' ) as $key ) {
					update_post_meta( $id, $key, get_post_meta( $source_id, $key, true ) );
				}
				$translations[ $lang ]   = $id;
				$tr_ids[ $lang ][ $slug ] = $id;
			}
			pll_save_post_translations( $translations );
		}

		// Related services point at the posts of the same language.
		foreach ( $tr_ids as $lang => $ids ) {
			foreach ( $ids as $slug => $id ) {
				$related = array();
				foreach ( (array) get_post_meta( $service_ids[ $slug ], '_bma_related_services', true ) as $rel ) {
					$rel_tr = (int) pll_get_post( (int) $rel, $lang );
					if ( $rel_tr ) {
						$related[] = $rel_tr;
					}
				}
				update_post_meta( $id, '_bma_related_services', $related );
			}
		}
	}
}
