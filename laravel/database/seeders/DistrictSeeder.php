<?php

namespace Database\Seeders;

use App\Models\City\City;
use App\Models\District\District;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Real Turkish districts for major cities
        $cityDistricts = [
            'Istanbul' => [
                'Fatih', 'Beyoğlu', 'Beşiktaş', 'Kadıköy', 'Üsküdar', 'Şişli', 
                'Bakırköy', 'Zeytinburnu', 'Esenler', 'Bahçelievler', 'Gaziosmanpaşa',
                'Sultangazi', 'Maltepe', 'Pendik', 'Kartal', 'Ümraniye', 'Ataşehir'
            ],
            'Ankara' => [
                'Çankaya', 'Keçiören', 'Yenimahalle', 'Mamak', 'Sincan', 'Etimesgut',
                'Altındağ', 'Gölbaşı', 'Pursaklar', 'Polatlı'
            ],
            'Izmir' => [
                'Konak', 'Karşıyaka', 'Bornova', 'Buca', 'Bayraklı', 'Gaziemir',
                'Alsancak', 'Balçova', 'Çiğli', 'Karabağlar'
            ],
            'Bursa' => [
                'Osmangazi', 'Nilüfer', 'Yıldırım', 'Mudanya', 'Gemlik', 'İnegöl',
                'Orhangazi', 'Kestel'
            ],
            'Antalya' => [
                'Muratpaşa', 'Konyaaltı', 'Kepez', 'Döşemealtı', 'Aksu', 'Manavgat',
                'Alanya', 'Side', 'Kaş'
            ],
            'Adana' => [
                'Seyhan', 'Yüreğir', 'Çukurova', 'Sarıçam', 'Ceyhan', 'Kozan'
            ],
            'Konya' => [
                'Meram', 'Selçuklu', 'Karatay', 'Ereğli', 'Akşehir', 'Beyşehir', 'Çumra'
            ],
            'Gaziantep' => [
                'Şahinbey', 'Şehitkamil', 'Oğuzeli', 'Nizip', 'Islahiye', 'Araban'
            ],
            'Kayseri' => [
                'Melikgazi', 'Kocasinan', 'Talas', 'İncesu', 'Develi', 'Bünyan'
            ],
            'Mersin' => [
                'Mezitli', 'Toroslar', 'Yenişehir', 'Akdeniz', 'Tarsus', 'Erdemli', 'Silifke'
            ],
            'Diyarbakir' => [
                'Bağlar', 'Kayapınar', 'Sur', 'Yenişehir', 'Bismil', 'Çınar', 'Ergani'
            ],
            'Eskisehir' => [
                'Odunpazarı', 'Tepebaşı', 'Mahmudiye', 'Çifteler', 'Sivrihisar', 'İnönü'
            ],
            'Sanliurfa' => [
                'Eyyübiye', 'Haliliye', 'Karaköprü', 'Viranşehir', 'Birecik', 'Siverek'
            ],
            'Trabzon' => [
                'Ortahisar', 'Akçaabat', 'Yomra', 'Araklı', 'Vakfıkebir', 'Of', 'Maçka'
            ],
            'Malatya' => [
                'Battalgazi', 'Yeşilyurt', 'Darende', 'Doğanşehir', 'Akçadağ', 'Arapgir'
            ],
            'Denizli' => [
                'Pamukkale', 'Merkezefendi', 'Acıpayam', 'Çivril', 'Buldan', 'Honaz'
            ],
            'Sakarya' => [
                'Adapazarı', 'Serdivan', 'Arifiye', 'Erenler', 'Sapanca', 'Hendek', 'Geyve'
            ],
            'Samsun' => [
                'İlkadım', 'Canik', 'Atakum', 'Tekkeköy', 'Bafra', 'Çarşamba', 'Terme'
            ],
            'Van' => [
                'İpekyolu', 'Tuşba', 'Edremit', 'Erciş', 'Başkale', 'Muradiye'
            ],
            'Kocaeli' => [
                'İzmit', 'Gebze', 'Darıca', 'Çayırova', 'Körfez', 'Gölcük', 'Başiskele'
            ]
        ];

        foreach (City::all() as $city) {
            // All cities now have real districts defined
            $districts = $cityDistricts[$city->name] ?? [];
            
            foreach ($districts as $districtName) {
                District::create([
                    'city_uuid' => $city->uuid,
                    'name' => $districtName,
                    'status' => 1
                ]);
            }
        }

        $this->command->info('Real Turkish districts seeded successfully.');
    }
}
