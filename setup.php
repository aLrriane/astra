<?php
/*
 * Astra - one-time setup
 * Open this page in your browser ONCE to create the tables and load the 10 starter objects.
 * Then DELETE this file from the server.
 */
require_once __DIR__ . '/includes/functions.php';

$seedJson = <<<'JSON'
{
 "categories": [
  {
   "slug": "star",
   "label": "Star",
   "name": "Stars",
   "definition": "A massive ball of hot plasma that generates its own light and heat through nuclear fusion.",
   "sort": 0
  },
  {
   "slug": "galaxy",
   "label": "Galaxy",
   "name": "Galaxies",
   "definition": "A vast, gravity-bound collection of stars, gas, and dust. Ours is one of billions.",
   "sort": 1
  },
  {
   "slug": "nebula",
   "label": "Nebula",
   "name": "Nebulae",
   "definition": "A cloud of gas and dust in space, sometimes glowing, sometimes the wreckage of a dead star.",
   "sort": 2
  },
  {
   "slug": "planet",
   "label": "Planet",
   "name": "Planets",
   "definition": "A body large enough to be rounded by its own gravity, orbiting a star.",
   "sort": 3
  },
  {
   "slug": "cluster",
   "label": "Star Cluster",
   "name": "Star Clusters",
   "definition": "A group of stars that formed together and are still loosely bound by gravity.",
   "sort": 4
  }
 ],
 "objects": [
  {
   "cat": "star",
   "name": "Betelgeuse",
   "location": "Constellation Orion · roughly 700 light-years away",
   "teaser": "A red supergiant so large its surface would reach past Mars if it replaced the Sun.",
   "description": "A red supergiant so large that if it replaced the Sun, its surface would reach past the orbit of Mars. It's nearing the end of its life and could go supernova at any point in the next 100,000 years, which in astronomical terms counts as any minute now.\n\nDespite its enormous size, it's surprisingly young for a star nearing death, at only around 8 to 10 million years old. Its exact distance is still debated, with estimates ranging from about 500 to 700 light-years.",
   "source": "https://en.wikipedia.org/wiki/Betelgeuse",
   "image": "images/Betelgeuse.webp",
   "featured": 1,
   "facts": [
    [
     "Distance",
     "~700 light-years (estimates vary)"
    ],
    [
     "Type",
     "Red Supergiant"
    ],
    [
     "Diameter",
     "~700x the Sun"
    ]
   ]
  },
  {
   "cat": "star",
   "name": "Sirius",
   "location": "Constellation Canis Major · 8.6 light-years away",
   "teaser": "The brightest star in Earth's night sky, and secretly a binary system.",
   "description": "The brightest star in Earth's night sky, easily spotted by following the three stars of Orion's Belt downward. It's actually a binary system, made up of a bright main star with a faint white dwarf companion orbiting it.\n\nAncient Egyptians tracked Sirius closely, since its yearly reappearance in the dawn sky lined up with the flooding of the Nile.",
   "source": "https://en.wikipedia.org/wiki/Sirius",
   "image": "images/Sirius.webp",
   "featured": 0,
   "facts": [
    [
     "Distance",
     "8.6 light-years"
    ],
    [
     "Type",
     "Binary star system"
    ],
    [
     "Also known as",
     "The Dog Star"
    ]
   ]
  },
  {
   "cat": "galaxy",
   "name": "Andromeda Galaxy",
   "location": "Constellation Andromeda · 2.5 million light-years away",
   "teaser": "The nearest large galaxy to our own, on a slow collision course with the Milky Way.",
   "description": "The nearest large galaxy to our own, and the only one visible to the naked eye from most of the Northern Hemisphere on a clear, dark night. It's on a slow collision course with the Milky Way, though current estimates for exactly when that happens range from about 4 to 10 billion years from now.\n\nRoughly twice the diameter of the Milky Way, Andromeda is home to somewhere around one trillion stars.",
   "source": "https://en.wikipedia.org/wiki/Andromeda_Galaxy",
   "image": "images/Andromeda_Galaxy.webp",
   "featured": 1,
   "facts": [
    [
     "Distance",
     "2.5 million light-years"
    ],
    [
     "Diameter",
     "~220,000 light-years"
    ],
    [
     "Also known as",
     "M31"
    ]
   ]
  },
  {
   "cat": "galaxy",
   "name": "Whirlpool Galaxy",
   "location": "Constellation Canes Venatici · 23 million light-years away",
   "teaser": "A textbook spiral galaxy, caught mid-interaction with a smaller companion.",
   "description": "A textbook spiral galaxy seen face-on, with clearly defined arms winding out from a bright core. It's caught mid-interaction with a smaller companion galaxy, whose gravity is actively reshaping its spiral structure.\n\nIt was one of the first galaxies ever observed to have a spiral shape, back when telescopes couldn't yet tell galaxies apart from nearby nebulae.",
   "source": "https://en.wikipedia.org/wiki/Whirlpool_Galaxy",
   "image": "images/Whirlpool_Galaxy.webp",
   "featured": 0,
   "facts": [
    [
     "Distance",
     "23 million light-years"
    ],
    [
     "Diameter",
     "~76,000 light-years"
    ],
    [
     "Also known as",
     "M51"
    ]
   ]
  },
  {
   "cat": "nebula",
   "name": "Orion Nebula",
   "location": "Constellation Orion · 1,344 light-years away",
   "teaser": "A vast stellar nursery visible even through ordinary binoculars.",
   "description": "A vast cloud of gas and dust where new stars are actively being born. It's visible even through ordinary binoculars as a faint, fuzzy patch just below Orion's Belt.\n\nOne of the most closely studied stellar nurseries in the sky, it's home to hundreds of young stars still wrapped in the material they formed from.",
   "source": "https://en.wikipedia.org/wiki/Orion_Nebula",
   "image": "images/Orion_nebula.webp",
   "featured": 0,
   "facts": [
    [
     "Distance",
     "1,344 light-years"
    ],
    [
     "Diameter",
     "~24 light-years"
    ],
    [
     "Also known as",
     "M42"
    ]
   ]
  },
  {
   "cat": "nebula",
   "name": "Crab Nebula",
   "location": "Constellation Taurus · 6,500 light-years away",
   "teaser": "The wreckage of a supernova recorded on Earth in the year 1054.",
   "description": "The wreckage of a star torn apart by a supernova explosion, one bright enough to be recorded by astronomers on Earth in the year 1054.\n\nAt its center spins a neutron star, the collapsed core of what used to be a much bigger star, rotating about 30 times every second.",
   "source": "https://en.wikipedia.org/wiki/Crab_Nebula",
   "image": "images/Crab_Nebula.webp",
   "featured": 0,
   "facts": [
    [
     "Distance",
     "6,500 light-years"
    ],
    [
     "Formed",
     "Supernova of 1054 AD"
    ],
    [
     "Also known as",
     "M1"
    ]
   ]
  },
  {
   "cat": "planet",
   "name": "Saturn",
   "location": "Solar System · 1.4 billion km from the Sun",
   "teaser": "Famous for its rings, and light enough that it would float in a big enough bathtub.",
   "description": "Famous for its rings, made of countless chunks of ice and rock ranging from dust grains to boulders. It's the least dense planet in the solar system, light enough that in theory it would float in a large enough bathtub.\n\nSaturn has more confirmed moons than any other planet, with the count still climbing as new ones are discovered.",
   "source": "https://en.wikipedia.org/wiki/Saturn",
   "image": "images/Saturn.webp",
   "featured": 1,
   "facts": [
    [
     "Distance from Sun",
     "1.4 billion km"
    ],
    [
     "Moons",
     "270+ confirmed"
    ],
    [
     "Type",
     "Gas Giant"
    ]
   ]
  },
  {
   "cat": "planet",
   "name": "Jupiter",
   "location": "Solar System · 778 million km from the Sun",
   "teaser": "The largest planet in the solar system, marked by a storm bigger than Earth.",
   "description": "The largest planet in the solar system, wrapped in colorful bands of swirling clouds and marked by the Great Red Spot, a storm bigger than Earth that's been raging for over 300 years.\n\nJupiter has such strong gravity that it acts like a shield, pulling in or deflecting many comets and asteroids that might otherwise threaten the inner solar system.",
   "source": "https://en.wikipedia.org/wiki/Jupiter",
   "image": "images/Jupiter_OPAL_2024.webp",
   "featured": 0,
   "facts": [
    [
     "Distance from Sun",
     "778 million km"
    ],
    [
     "Moons",
     "95+ confirmed"
    ],
    [
     "Type",
     "Gas Giant"
    ]
   ]
  },
  {
   "cat": "cluster",
   "name": "The Pleiades",
   "location": "Constellation Taurus · 444 light-years away",
   "teaser": "A tight cluster of young blue stars, named by cultures worldwide for millennia.",
   "description": "A tight cluster of hot, young blue stars wrapped in wisps of reflection nebula, visible to the naked eye as a small, dipper-shaped smudge in the sky.\n\nCultures across the world have named and mythologized this cluster for thousands of years, long before telescopes existed.",
   "source": "https://en.wikipedia.org/wiki/Pleiades",
   "image": "images/Pleiades_large.webp",
   "featured": 0,
   "facts": [
    [
     "Distance",
     "444 light-years"
    ],
    [
     "Stars",
     "1,000+ (6-7 visible to the eye)"
    ],
    [
     "Also known as",
     "M45, Seven Sisters"
    ]
   ]
  },
  {
   "cat": "cluster",
   "name": "Hercules Cluster",
   "location": "Constellation Hercules · about 22,000 light-years away",
   "teaser": "A dense ball of roughly 300,000 stars, tight enough for stars to collide.",
   "description": "A tightly packed ball of several hundred thousand stars, held together by gravity far more densely than a normal star cluster. Near its center, stars are so crowded that collisions between them are actually possible.\n\nIt's one of the few deep-sky objects bright enough to see with the naked eye under a dark sky, appearing as a small fuzzy patch in the constellation Hercules.",
   "source": "https://en.wikipedia.org/wiki/Messier_13",
   "image": "images/Hercules_Cluster.webp",
   "featured": 0,
   "facts": [
    [
     "Distance",
     "~22,000 light-years"
    ],
    [
     "Stars",
     "~300,000"
    ],
    [
     "Also known as",
     "M13"
    ]
   ]
  }
 ]
}
JSON;
$seed = json_decode($seedJson, true);

$pdo = db();
$log = array();

if (isset($_GET['reset'])) {
    $pdo->exec('DROP TABLE IF EXISTS ' . t('facts'));
    $pdo->exec('DROP TABLE IF EXISTS ' . t('objects'));
    $pdo->exec('DROP TABLE IF EXISTS ' . t('categories'));
    $log[] = 'Dropped existing Astra tables.';
}

$pdo->exec('CREATE TABLE IF NOT EXISTS ' . t('categories') . ' (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(50) NOT NULL UNIQUE,
    label VARCHAR(100) NOT NULL,
    name VARCHAR(100) NOT NULL,
    definition TEXT NOT NULL,
    sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

$pdo->exec('CREATE TABLE IF NOT EXISTS ' . t('objects') . ' (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    location VARCHAR(255) NOT NULL,
    teaser VARCHAR(500) NOT NULL,
    description TEXT NOT NULL,
    source_url VARCHAR(500) NULL,
    image VARCHAR(255) NOT NULL,
    featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES ' . t('categories') . ' (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

$pdo->exec('CREATE TABLE IF NOT EXISTS ' . t('facts') . ' (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    object_id INT UNSIGNED NOT NULL,
    label VARCHAR(100) NOT NULL,
    value VARCHAR(255) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    FOREIGN KEY (object_id) REFERENCES ' . t('objects') . ' (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
$log[] = 'Tables are ready.';

$existing = (int)$pdo->query('SELECT COUNT(*) FROM ' . t('categories'))->fetchColumn();
if ($existing > 0) {
    $log[] = 'Starter data was already loaded, so nothing was added. (Add ?reset=1 to the address to start over.)';
} else {
    $pdo->beginTransaction();
    $catIds = array();
    $catStmt = $pdo->prepare('INSERT INTO ' . t('categories') . ' (slug, label, name, definition, sort_order) VALUES (?, ?, ?, ?, ?)');
    foreach ($seed['categories'] as $c) {
        $catStmt->execute(array($c['slug'], $c['label'], $c['name'], $c['definition'], $c['sort']));
        $catIds[$c['slug']] = (int)$pdo->lastInsertId();
    }
    $objStmt = $pdo->prepare('INSERT INTO ' . t('objects') . ' (category_id, name, location, teaser, description, source_url, image, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $factStmt = $pdo->prepare('INSERT INTO ' . t('facts') . ' (object_id, label, value, sort_order) VALUES (?, ?, ?, ?)');
    foreach ($seed['objects'] as $o) {
        $objStmt->execute(array($catIds[$o['cat']], $o['name'], $o['location'], $o['teaser'], $o['description'], $o['source'], $o['image'], $o['featured']));
        $oid = (int)$pdo->lastInsertId();
        foreach ($o['facts'] as $i => $f) {
            $factStmt->execute(array($oid, $f[0], $f[1], $i));
        }
    }
    $pdo->commit();
    $log[] = 'Loaded ' . count($seed['categories']) . ' categories and ' . count($seed['objects']) . ' objects.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Astra setup</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<main class="page">
    <div class="wrap">
        <h1>Astra setup</h1>
        <?php foreach ($log as $line): ?><p><?= e($line) ?></p><?php endforeach; ?>
        <p>Tables created with the prefix <strong><?= e(STUDENT_ID) ?>_astra_</strong></p>
        <p><strong>Next:</strong> delete <code>setup.php</code> from the server, then <a href="index.php" style="color: var(--accent);">open the site</a>.</p>
    </div>
</main>
</body>
</html>
