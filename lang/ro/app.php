<?php

declare(strict_types=1);

return [

    'banner' => 'O soluție Code for Romania.',
    'skip' => 'Sari la conținut',
    'backHome' => 'Înapoi la pagina principală',
    'all' => 'Toate',
    'generate' => 'Generează',

    'field' => [
        'acronym' => 'Acronim',
        'aliases' => 'Aliasuri',
        'color' => 'Culoare',
        'candidate' => 'Partid / Alianță / Candidat independent',
        'country' => 'Țară',
        'county' => 'Județ',
        'level' => 'Nivel',
        'has_lists' => 'Are liste',
        'created_at' => 'Creat la',
        'cron' => 'Interval',
        'date' => 'Dată',
        'default_tab' => 'Tab implicit',
        'email' => 'Email',
        'id' => 'ID',
        'image' => 'Imagine',
        'initial_complement' => 'Înscriși pe listele complementare',
        'initial_permanent' => 'Înscriși pe listele permanente',
        'is_enabled' => 'Activ',
        'is_live' => 'Live',
        'is_visible' => 'Vizibil',
        'job' => 'Job',
        'last_run_at' => 'Ultima rulare',
        'locality' => 'Localitate',
        'location' => 'Locație',
        'logo' => 'Logo',
        'name' => 'Nume',
        'party' => 'Partid',
        'role' => 'Rol',
        'section' => 'Secție',
        'siruta' => 'ID SIRUTA',
        'slug' => 'Slug',
        'source_part' => 'Parte',
        'source_password' => 'Parolă',
        'change_password' => 'Schimbă parola',
        'default_place' => 'Locație implicită',
        'show_threshold' => 'Afișează prag',
        'source_url' => 'URL sursă',
        'source_username' => 'Utilizator',
        'subtitle' => 'Subtitlu',
        'tabs' => 'Taburi',
        'total_seats' => 'Total mandate',
        'title' => 'Titlu',
        'type' => 'Tip',
        'updated_at' => 'Actualizat la',
        'voters_complement' => 'Votanți pe liste complementare',
        'voters_mobile' => 'Votanți cu urnă mobilă',
        'voters_percent' => 'Prezență',
        'voters_permanent' => 'Votanți pe liste permanente',
        'voters_supplement' => 'Votanți pe liste suplimentare',
        'voters_total' => 'Total votanți',
        'year' => 'An',
        'description' => 'Descriere',
        'eligible_voters_total' => 'Numărătoare: total alegători înscriși pe liste',
        'present_voters_total' => 'Numărătoare: total alegători prezenți la urne',
        'votes_valid' => 'Total voturi valabil exprimate',
        'votes_null' => 'Total voturi nule',
    ],

    'navigation' => [
        'admin' => 'Administrare',
        'election_data' => 'Date alegeri',
        'elections' => 'Alegeri',
        'nomenclature' => 'Nomenclatoare',
        'turnout' => 'Prezența la vot',
        'results' => 'Rezultate vot',
    ],

    'election' => [
        'label' => [
            'singular' => 'alegere',
            'plural' => 'alegeri',
        ],

        'settings' => 'Setări rundă electorală',
    ],

    'election_type' => [
        'presidential' => 'Alegeri prezidențiale',
        'parliamentary' => 'Alegeri parlamentare',
        'euro' => 'Alegeri europarlamentare',
        'local' => 'Alegeri locale',
        'referendum' => 'Referendum',
    ],

    'country' => [
        'label' => [
            'singular' => 'țară',
            'plural' => 'țări',
        ],
    ],

    'county' => [
        'label' => [
            'singular' => 'județ',
            'plural' => 'județe',
        ],
    ],

    'locality' => [
        'label' => [
            'singular' => 'localitate',
            'plural' => 'localități',
        ],
    ],

    'candidate' => [
        'label' => [
            'singular' => 'candidat',
            'plural' => 'candidați',
        ],

        'action' => [
            'show' => 'Afișează toți candidații',
            'hide' => 'Ascunde candidații',
        ],
    ],

    'part' => [
        'prov' => 'Provizorii',
        'part' => 'Parțiale',
        'final' => 'Finale',
    ],

    'party' => [
        'label' => [
            'singular' => 'partid',
            'plural' => 'partide',
        ],
    ],

    'record' => [
        'label' => [
            'singular' => 'proces verbal',
            'plural' => 'procese verbale',
        ],
    ],

    'turnout' => [
        'label' => 'prezență',
    ],

    'vote' => [
        'label' => [
            'singular' => 'vot',
            'plural' => 'voturi',
        ],
    ],

    'mandate' => [
        'label' => [
            'singular' => 'mandat',
            'plural' => 'mandate',
        ],

        'action' => [
            'generate' => 'Generează mandate',
        ],
    ],

    'scheduled_job' => [
        'label' => [
            'singular' => 'job programat',
            'plural' => 'joburi programate',
        ],
    ],

    'user' => [
        'label' => [
            'singular' => 'utilizator',
            'plural' => 'utilizatori',
        ],

        'role' => [
            'admin' => 'Administrator',
            'contributor' => 'Contribuitor',
            'viewer' => 'Vizitator',
        ],
    ],

    'page' => [
        'label' => [
            'singular' => 'pagină',
            'plural' => 'pagini',
        ],
    ],

    'cron' => [
        'every_minute' => '1 minut',
        'every_2_minutes' => '2 minute',
        'every_3_minutes' => '3 minute',
        'every_4_minutes' => '4 minute',
        'every_5_minutes' => '5 minute',
        'every_10_minutes' => '10 minute',
        'every_5_1_minutes' => '5+1 minute',
        'every_5_2_minutes' => '5+2 minute',
        'every_5_3_minutes' => '5+3 minute',
        'every_5_4_minutes' => '5+4 minute',
        'every_10_5_minutes' => '10+5 minute',
        'every_10_6_minutes' => '10+6 minute',
        'every_10_7_minutes' => '10+7 minute',
        'every_10_8_minutes' => '10+8 minute',
        'every_10_9_minutes' => '10+9 minute',
    ],

    'area' => [
        'urban' => 'Urban',
        'rural' => 'Rural',
    ],

    'data_level' => [
        'total' => 'Total',
        'national' => 'România',
        'diaspora' => 'Diaspora',
    ],

    'others' => 'Alții',

    'vote_monitor_stats' => [
        'observers' => 'Observatori logați în aplicație',
        'counties' => 'Județe acoperite',
        'polling_stations' => 'Secții de votare acoperite',
        'messages' => 'Mesaje trimise de către observatori',
        'problems' => 'Probleme sesizate',
    ],

    'article' => [
        'singular' => 'Articol',
        'plural' => 'Articole',
        'title' => 'Titlu',
        'author' => 'Autor',
        'election' => 'Rundă electorală',
        'published_at' => 'Publicat la',
        'content' => 'Conținut',
        'embeds' => 'Embeduri',
    ],

    'newsfeed' => [
        'title' => 'Live newsfeed',
        'description' => 'Aici vezi ultimele știri și informații relevante acestei alegeri, culese din surse de încredere de către echipa Code for Romania.',
        'more' => 'Vezi mai mult',
    ],
];
