<?php

namespace App\DataFixtures;

use App\Entity\Props\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CitiesFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $cities = [
            'vilnius' => 'Vilnius',
            'kaunas' => 'Kaunas',
            'klaipeda' => 'Klaipėda',
            'siauliai' => 'Šiauliai',
            'panevezys' => 'Panevėžys',
            'alytus' => 'Alytus',
            'marijampole' => 'Marijampolė',
            'mazeikiai' => 'Mažeikiai',
            'jonava' => 'Jonava',
            'utena' => 'Utena',
            'kedainiai' => 'Kėdainiai',
            'telsiai' => 'Telšiai',
            'ukmerge' => 'Ukmergė',
            'taurage' => 'Tauragė',
            'visaginas' => 'Visaginas',
            'plunge' => 'Plungė',
            'kretinga' => 'Kretinga',
            'palanga' => 'Palanga',
            'silute' => 'Šilutė',
            'radviliskis' => 'Radviliškis',
            'gargzdai' => 'Gargždai',
            'druskininkai' => 'Druskininkai',
            'rokiskis' => 'Rokiškis',
            'elektrenai' => 'Elektrėnai',
            'kursenai' => 'Kuršėnai',
            'birzai' => 'Biržai',
            'garliava' => 'Garliava',
            'vilkaviskis' => 'Vilkaviškis',
            'jurbarkas' => 'Jurbarkas',
            'grigiskes' => 'Grigiškės',
            'raseiniai' => 'Raseiniai',
            'lentvaris' => 'Lentvaris',
            'prienai' => 'Prienai',
            'anyksciai' => 'Anykščiai',
            'joniskis' => 'Joniškis',
            'kaisiadorys' => 'Kaišiadorys',
            'varena' => 'Varėna',
            'naujoji_akmene' => 'Naujoji Akmenė',
            'kelme' => 'Kelmė',
            'salcininkai' => 'Šalčininkai',
            'pasvalys' => 'Pasvalys',
            'kupiskis' => 'Kupiškis',
            'zarasai' => 'Zarasai',
            'sirvintos' => 'Širvintos',
            'moletai' => 'Molėtai',
            'kazlu_ruda' => 'Kazlų Rūda',
            'skuodas' => 'Skuodas',
            'sakiai' => 'Šakiai',
            'trakai' => 'Trakai',
            'ignalina' => 'Ignalina',
            'pabrade' => 'Pabradė',
            'nemencine' => 'Nemenčinė',
            'svencioneliai' => 'Švenčionėliai',
            'silale' => 'Šilalė',
            'pakruojis' => 'Pakruojis',
            'svencionys' => 'Švenčionys',
            'vievis' => 'Vievis',
            'kybartai' => 'Kybartai',
            'lazdijai' => 'Lazdijai',
            'kalvarija' => 'Kalvarija',
            'neringa' => 'Neringa',
            'rietavas' => 'Rietavas',
            'ziezmariai' => 'Žiežmariai',
            'birstonas' => 'Birštonas',
            'eisiskes' => 'Eišiškės',
            'ariogala' => 'Ariogala',
            'seduva' => 'Šeduva',
            'akmene' => 'Akmenė',
            'venta' => 'Venta',
            'vieksniai' => 'Viekšniai',
            'rudiskes' => 'Rūdiškės',
            'tytuvėnai' => 'Tytuvėnai',
            'vilkija' => 'Vilkija',
            'ezerelis' => 'Ežerėlis',
            'pagegiai' => 'Pagėgiai',
            'gelgaudiskis' => 'Gelgaudiškis',
            'skaudvile' => 'Skaudvilė',
            'kudirkos_naumiestis' => 'Kudirkos Naumiestis',
            'zagare' => 'Žagarė',
            'linkuva' => 'Linkuva',
            'salantai' => 'Salantai',
            'simnas' => 'Simnas',
            'priekule' => 'Priekulė',
            'ramygala' => 'Ramygala',
            'veisiejai' => 'Veisiejai',
            'jieznas' => 'Jieznas',
            'joniskelis' => 'Joniškėlis',
            'daugai' => 'Daugai',
            'baltoji_voke' => 'Baltoji Vokė',
            'seda' => 'Seda',
            'virbalis' => 'Virbalis',
            'varniai' => 'Varniai',
            'obeliai' => 'Obeliai',
            'subacius' => 'Subačius',
            'vabalninkas' => 'Vabalninkas',
            'smalininkai' => 'Smalininkai',
            'dukstas' => 'Dūkštas',
            'pandelys' => 'Pandėlys',
            'uzventis' => 'Užventis',
            'dusetos' => 'Dusetos',
            'kavarskas' => 'Kavarskas',
            'troskunai' => 'Troškūnai',
            'panemune' => 'Panemunė',
        ];

        foreach ($cities as $cityKey => $city) {
            $newCity = new City();
            $newCity->setName($cityKey);
            $newCity->setTitle($city);
            $manager->persist($newCity);
        }

        $manager->flush();
    }
}
