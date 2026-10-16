<?php
class ReviewController extends Controller
{
    public function index(): void
    {
        $this->view('reviews/index', ['title' => t('reviews.mine'), 'reviews' => Review::mine(user()['id'])]);
    }

    public function store(): void
    {
        $pid = (int) ($_POST['product_id'] ?? 0);
        $rating = (int) ($_POST['rating'] ?? 0);
        $comment = $this->input('comment');
        $back = '/products/detail?id=' . $pid;
        $orderId = Review::eligibleOrder(user()['id'], $pid);
        if (!$orderId) { flash('error', t('reviews.not_allowed')); $this->redirect($back); }
        if ($rating < 1 || $rating > 5 || str_len($comment) > 1000) { flash('error', t('reviews.invalid')); $this->redirect($back); }
        try {
            Model::insert('reviews', ['user_id' => user()['id'], 'product_id' => $pid, 'order_id' => $orderId, 'rating' => $rating, 'comment' => $comment, 'status' => 1]);
            flash('success', t('reviews.thanks'));
        } catch (PDOException $ex) {
            flash('error', t('reviews.duplicate'));
        }
        $this->redirect($back);
    }
}
