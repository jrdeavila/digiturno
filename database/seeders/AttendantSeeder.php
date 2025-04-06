<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            '1082244580' => ['nombre' => 'Deisy Paola Rivera Ospino', 'correo' => 'auxiliarventanilla1@ccvalledupar.org.co'],
            '1108763834' => ['nombre' => 'Maria José Contreras Cruz', 'correo' => 'auxiliarventanilla3@ccvalledupar.org.co'],
            '1235339427' => ['nombre' => 'Laura Marcela Parra Mejía', 'correo' => 'auxiliarderegistro@ccvalledupar.org.co'],
            '1066296792' => ['nombre' => 'Tiziana Valentina Cañate Bandera', 'correo' => 'auxiliarinformacion1@ccvalledupar.org.co'],
            '1065840752' => ['nombre' => 'María de los Reyes Rivero Durán', 'correo' => 'auxiliarventanilla10@ccvalledupar.org.co'],
            '1068392292' => ['nombre' => 'Eva Sandrith Pedroza Palis', 'correo' => 'asesorempresarial5@ccvalledupar.org.co'],
            '55312961'   => ['nombre' => 'Yadis Ibeth Guerrero Chinchilla', 'correo' => 'auxiliardocumental4@ccvalledupar.org.co'],
            '1065661252' => ['nombre' => 'Katerine Elvira Roy García', 'correo' => 'asesorempresarial3@ccvalledupar.org.co'],
            '1193565807' => ['nombre' => 'María José Araujo Arzuaga', 'correo' => 'judicante1@ccvalledupar.org.co'],
            '1003265711' => ['nombre' => 'Camila Dangond Llanes', 'correo' => 'juridica3@ccvalledupar.org.co'],
            '1065591054' => ['nombre' => 'José Daniel Gutiérrez Maya', 'correo' => 'profenderecho@ccvalledupar.org.co'],
            '17974482'   => ['nombre' => 'José Jaime Fuentes Vence', 'correo' => 'centrodeatencionempresarial@ccvalledupar.org.co'],
            '1003265279' => ['nombre' => 'Ana María Iseda Acosta', 'correo' => 'auxiliarventanilla2@ccvalledupar.org.co'],
            '26946163'   => ['nombre' => 'Anayibe Conde García', 'correo' => 'auxiliarventanilla8@ccvalledupar.org.co'],
            '37369501'   => ['nombre' => 'Lidgia María Espinel Trujillo', 'correo' => 'asesorempresarial4@ccvalledupar.org.co'],
        ];

        foreach ($data as $cedula => $attendant) {
            \App\Models\Attendant::create([
                'dni' => $cedula,
                'name' => $attendant['nombre'],
                'email' => $attendant['correo'],
                'enabled' => true,
                'password' => bcrypt($cedula),
            ]);
        }
    }
}
