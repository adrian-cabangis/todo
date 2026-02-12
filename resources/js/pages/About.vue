<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'About',
    },
];
</script>

<template>
    <Head title="About" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit.
                Voluptatibus reiciendis dolor hic eos et ipsa sint? Soluta
                repellat totam fugiat vitae facere sit illo commodi. Aspernatur,
                aut harum delectus vero alias at laborum consequatur fugit.
                Delectus nihil, numquam libero ab necessitatibus, alias dolorum
                commodi, quae possimus sapiente animi. Debitis quia maiores
                accusantium quam soluta repudiandae odit quae explicabo ipsa
                repellendus. Accusantium cupiditate vero harum architecto odio
                officia delectus deserunt quas, maiores est asperiores earum
                magnam. Neque expedita ratione, saepe nisi impedit totam illo
                aperiam ipsum fugit quasi quos consequatur eveniet repellat qui
                deserunt necessitatibus, in nobis quisquam ullam iure accusamus!
                Libero itaque veniam aliquid enim, ducimus recusandae similique
                quae perferendis dolore a tempora veritatis esse fuga ullam
                laudantium praesentium temporibus pariatur sed natus nisi ut
                sunt eius. Consequuntur error quaerat cupiditate laborum
                dignissimos eveniet, similique commodi, ut, asperiores
                aspernatur autem molestiae? Porro tempore deleniti veniam
                maiores, illum fugit reiciendis iusto dicta eum odio, expedita
                voluptatum quibusdam quisquam ullam hic velit consequatur
                consectetur laborum totam unde. Cum libero exercitationem
                obcaecati, aut quis eaque perspiciatis atque nobis nostrum! Eum,
                iste sapiente nulla tenetur, corporis enim esse mollitia et odit
                fuga molestias! Unde rem reprehenderit consequuntur veniam
                quidem commodi excepturi? Eius, iusto adipisci harum deserunt id
                natus similique quasi asperiores libero quidem quaerat dolor
                iste nihil perspiciatis! Laboriosam maiores ad amet sapiente
                eveniet officiis excepturi maxime laudantium. Laudantium nam
                omnis, nulla excepturi necessitatibus debitis consequuntur
                praesentium! Magni dolores officiis omnis facere eum, expedita
                doloribus numquam praesentium doloremque accusantium quod? Iste,
                autem esse saepe in voluptate vero exercitationem, numquam nulla
                unde ut, atque omnis? Adipisci possimus qui pariatur recusandae
                veniam vitae atque nobis iste, incidunt debitis facilis cum ad
                odit praesentium consectetur ea porro reprehenderit quisquam
                natus aliquid quibusdam! Magni dicta quis id dolores quae culpa
                explicabo velit ipsam consectetur! Molestias delectus libero
                veritatis aut amet molestiae qui dignissimos ullam consequatur
                similique quo perferendis modi, sed nostrum cum suscipit fugiat.
                Quaerat dolores, ratione accusantium a officia, modi enim nemo
                sequi quod beatae consequatur error doloribus cum animi ipsa ut
                iure velit quas aperiam commodi pariatur harum ullam! Doloremque
                tempore consequuntur sed, sapiente incidunt quisquam eligendi
                asperiores? Iusto iste rem praesentium dolor harum at eius
                ratione sit odit? Beatae facilis, velit dicta corporis minima,
                voluptatibus dignissimos magnam consequuntur eveniet et qui ab
                natus in nobis illo, assumenda laborum consectetur? Enim,
                inventore quod ipsa deleniti, veniam praesentium laudantium esse
                aut quas quisquam nulla quo unde dolore fugit totam quis,
                nostrum excepturi modi culpa laboriosam. Quaerat esse
                repudiandae similique nesciunt eum sit dolores ipsa quod
                excepturi cumque dolorem maiores debitis, eligendi adipisci
                molestias nisi consequatur, cupiditate iusto perspiciatis
                corrupti facere totam magnam tempora dignissimos. Veniam
                laudantium facere dolor, maiores eveniet suscipit laborum
                laboriosam ipsum, delectus iusto in tempora vero optio ipsa.
                Veritatis atque quos quaerat doloribus, vitae in assumenda ipsam
                voluptatum rem. Iusto minima, omnis exercitationem similique
                voluptates enim tenetur, nisi nobis mollitia commodi vero
                voluptatibus quae modi ipsam? Aliquid nesciunt dolorum
                praesentium aut inventore corrupti voluptates minus, iste quam
                cumque. A deserunt minima architecto distinctio odit impedit
                quasi quo, aliquam iure quisquam at commodi beatae facilis
                recusandae eius aliquid sed cum sequi eos adipisci explicabo et
                similique cupiditate? Unde quo distinctio corporis repellat nam
                beatae possimus!
            </p>
        </div>
    </AppLayout>
</template>
