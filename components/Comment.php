<div class="bg-branddark-600 flex flex-row p-2 gap-5 rounded-xl">
  <a href="/profile/<?= $comment->getAuthor()->id ?>" class="w-16 h-16 rounded-full overflow-hidden border-4 border-alt-500">
    <img src="/media/profile/<?= $comment->getAuthor()->profile_pic ?? 'default' ?>" alt="Foto de perfil de <?= $comment->getAuthor()->username ?>" class="w-full h-full object-cover">
  </a>
  <div class="flex flex-col gap-2 w-full mr-4">
    <a href="/profile/<?= $comment->getAuthor()->id ?>" class="font-semibold text-gray-200"><?= $comment->getAuthor()->username ?></a>
    <p class="text-gray-200 w-full"><?= $comment->body ?></p>   
  </div>
</div>