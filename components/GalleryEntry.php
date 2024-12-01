<div class="max-w-xl mx-auto bg-branddark rounded-lg shadow-md overflow-hidden">
    <div class="p-4">
        <h1 class="text-2xl font-bold text-gray-200">
            <?= $post->title ?>
        </h1>
        <span class="text-gray-400 text-sm">Publicado por <a href="/profile/<?= $post->getAuthor()->id ?>" class="font-semibold hover:text-gray-300 link-underline"><?= $post->getAuthor()->username ?></a></span>
    </div>


    <div data-galleryslot="media" data-uuid="<?=$galleryInfo->media?>">
        
    </div>

    <!-- Componente de voto -->
    <div class="flex items-center justify-between p-4 border-t">
        <div class="flex flex-row gap-2">
            <div data-galleryslot="shareBtn" data-postid="<?=$post->id?>" class="w-8 h-8 rounded-full bg-alt hover:bg-alt-600 cursor-pointer flex items-center justify-center">
                <i class="bi bi-share-fill text-brand"></i>
            </div>
            <div data-galleryslot="shareLink" class="hidden w-64 p-2 bg-alt border border-alt-600 rounded">
                <input data-galleryslot="linkInput" type="text" class="w-full p-2 border rounded bg-brand text-gray-200" readonly>
            </div>
        </div>
        <div class="flex items-center justify-between gap-2">
            <span data-galleryslot="value"><?=$galleryInfo->getValue()?></span>
            <?php if(isset($_SESSION['user'])) { ?>
                <gallery-vote value="<?=$value?>"></gallery-vote>
            <?php } else { ?>
                <a href="/login" class="w-8 h-8 rounded-full bg-alt hover:bg-alt-600 cursor-pointer flex items-center justify-center">
                    <i class="bi bi-star text-brand"></i>
                </a>
            <?php } ?>
        </div>
    </div>
</div>