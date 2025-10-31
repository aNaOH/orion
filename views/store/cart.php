<?php

$title = "Carro | Orion Store";

function showPage()
{
    $gamesInCart = OrderHelper::getInstances();
    $order = OrderHelper::getOrder();
    $total = OrderHelper::getTotal();
    $user = $_SESSION["user"] ?? null;
    $email = $user ? User::getById($user["id"])->email : null;
    ?>

    <h1 class="text-3xl font-bold my-4">Carro</h1>

    <?php if (empty($gamesInCart)): ?>
        <div class="text-center py-16 text-gray-300">
            <p class="text-xl mb-4">Tu carro está vacío</p>
            <a href="/store" class="text-alt font-semibold hover:underline">Explorar la tienda</a>
        </div>
        <?php return; ?>
    <?php endif; ?>

    <div class="bg-branddark rounded-xl shadow p-4 my-6">
        <ul class="divide-y divide-alt">
        <?php foreach ($gamesInCart as $item): ?>
        <li class="flex items-center justify-between gap-4 py-3">

            <!-- Imagen + título -->
            <div class="flex items-center gap-3 flex-1">
                <img src="/media/game/thumb/<?= $item->id ?>"
                     alt="<?= htmlspecialchars($item->title) ?>"
                     class="aspect-[2.14/1] w-32 rounded-md shadow-lg object-cover">
                <span class="text-gray-200"><?= htmlspecialchars(
                    $item->title,
                ) ?></span>
            </div>

            <!-- Precios -->
            <div class="flex text-right gap-3">
                <?php $discounted =
                    $item->base_price - $item->base_price * $item->discount; ?>
                <?php if ($item->discount > 0): ?>
                    <div class="flex flex-col leading-tight">
                        <span class="text-green-400 text-xs font-semibold">-<?= $item->discount *
                            100 ?>%</span>
                        <span class="text-gray-400 line-through text-xs"><?= number_format(
                            $item->base_price,
                            2,
                        ) ?> €</span>
                        <span class="text-gray-200 font-semibold"><?= number_format(
                            $discounted,
                            2,
                        ) ?> €</span>
                    </div>
                <?php else: ?>
                    <span class="text-gray-200 font-semibold"><?= number_format(
                        $item->base_price,
                        2,
                    ) ?> €</span>
                <?php endif; ?>
            </div>

            <!-- Botón eliminar -->
            <button data-id="<?= $item->id ?>"
                    class="text-gray-400 hover:text-red-400 transition delete-item">
                <i class="bi bi-trash-fill text-lg"></i>
            </button>

        </li>
        <?php endforeach; ?>
        </ul>
    </div>

    <!-- Total + Pago -->
    <div class="bg-branddark rounded-xl shadow p-4 my-6 space-y-4">
        <div class="flex justify-between text-gray-200 font-semibold">
            <span>Total:</span>
            <span><?= $total > 0
                ? number_format($total, 2) . " €"
                : "Gratis" ?></span>
        </div>

        <?php if ($total > 0): ?>
            <div class="flex flex-col space-y-4">
                <div id="billing-element" class="bg-brand-200 p-4 shadow-md rounded-md"></div>
                <div id="payment-element" class="shadow-md rounded-md"></div>
            </div>
        <?php endif; ?>

        <button id="pay-btn"
                class="w-full px-5 py-2 rounded-md shadow-md font-semibold text-white bg-alt hover:bg-alt-400 transition-all flex justify-center gap-2">
            <span id="pay-btn-text"><?= $total > 0
                ? "Pagar"
                : "Obtener" ?></span>
            <span id="pay-btn-spinner" class="hidden animate-spin">
                <i class="bi bi-arrow-repeat"></i>
            </span>
        </button>
    </div>

    <script src="https://js.stripe.com/clover/stripe.js"></script>
    <script>
      const email = <?= json_encode($email) ?>;
      const total = <?= json_encode($total) ?>;
      const order = <?= json_encode($order) ?>;

      // Delete item
      document.querySelectorAll('.delete-item').forEach(btn => {
          btn.addEventListener('click', () => {
              fetch(`/api/cart/${btn.dataset.id}`, { method: "DELETE" })
                  .then(() => location.reload());
          });
      });

      function setPayBtnState(disabled, showSpinner = false) {
          const btn = document.getElementById('pay-btn');
          document.getElementById('pay-btn-text').classList.toggle('hidden', showSpinner);
          document.getElementById('pay-btn-spinner').classList.toggle('hidden', !showSpinner);
          btn.disabled = disabled;
      }

      const stripe = Stripe('<?= $_ENV["STRIPE_PUBLIC_KEY"] ?>');
      let checkout;
      let actions;
      let loadActionsResult;

      document.addEventListener('DOMContentLoaded', async () => {
          const payBtn = document.getElementById('pay-btn');
          const cardElement = document.getElementById('stripe-element');

          const promise = fetch('/api/order', {
              method: 'POST',
              headers: {'Content-Type': 'application/json'},
            })
              .then((r) => r.json())
              .then((r) => r.client_secret);

            const appearance = {
              theme: 'stripe',
            };
            checkout = stripe.initCheckout({
              clientSecret: promise,
              elementsOptions: { appearance },
            });

            const loadActionsResult = await checkout.loadActions();
            if (loadActionsResult.type === 'success') {
                actions = loadActionsResult.actions;
              }

            const paymentElement = checkout.createPaymentElement();
              paymentElement.mount("#payment-element");
              const billingAddressElement = checkout.createBillingAddressElement();
              billingAddressElement.mount("#billing-element");

          payBtn.addEventListener('click', async () => {
              setPayBtnState(true, true);

              if (total === 0) {
                  await fetch('/api/order/save', {
                      method: 'POST',
                      headers: { 'Content-Type': 'application/json' },
                      body: JSON.stringify({ order, email })
                  });
                  return location.href = "/library";
              }

              if (loadActionsResult.type === 'success') {
                  const { error } = await loadActionsResult.actions.confirm({
                    redirect: 'if_required'
                  });
                  if(error) {
                      return setPayBtnState(false, false);
                  }
                }



              await fetch('/api/order/save', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json' },
                  body: JSON.stringify({ order, email, stripe_id: loadActionsResult.actions.getSession().id })
              });

              setPayBtnState(false, false);

              //location.href = "/library";
          });
      });
    </script>

<?php
}

include "views/templates/main.php";
