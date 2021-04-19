<?php /* Template Name: Auction Page */

get_header();
if(is_user_logged_in()){

    $user_id = get_current_user_id();
    $auction_list = get_user_meta($user_id, 'interested_auction');
}
else{
    $auction_list = array();
    if(isset($_GET['event_id'])){
        $auction_list = explode(" ", $_GET['event_id']);
    }
}
?>
    <section class="jumbotron text-center bg-white event-list-banner">
        <span class="mask bg-gradient-default opacity-4 event-list-mask"></span>
        <div class="container vertical-centered">

            <div class="mb-2">
                <h3 class="jumbotron-heading mb-3 text-white auction-list-header">Search Ongoing Events</h3>
            </div>
            <div class="form-group">
            <form class="d-flex justify-content-center">
                <div class="col-md-6">
                    <div id="div_textbox">
                        <input class="form-control" id="search-textbox" type="search" placeholder="Search Auction Event By Address" aria-label="Search">
                    </div>
                </div>
            </form>
            <?php if(!is_user_logged_in()) : ?>
                <div class="alert alert-warning mt-3" role="alert" style="display: none">
                    <p>
                        You have not logged in yet. You may still add and watch an auction event, however 
                        the list will not be saved. You might lost all the events you have added after leaving or refreshing
                        the page. Please click here to <a href="<?php echo home_url()."/login"?>">login</a>
                    </p>
                </div>
            <?php endif ;?>
        </div>
    </section>

    <div class="auctions-list-wrapper">
        <div class="text-center bg-white zhijie-auctions-list py-2">
            <h2>My Interested Auctions</h2>
                <?php if($auction_list) : ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Address</th>
                                    <th scope="col">Begin at</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Price (AUD$)</th>
                                    <th scope="col">Calls</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($auction_list as $auction) : ?>
                                <?php 
                                    $auction_new_info = find_event_new_info_by_id($auction);
                                    $auction_basic_info = find_event_info_in_zhijie_events($auction);

                                    if( $auction_new_info && $auction_basic_info ) :
                                ?>
                                    <tr class="zhijie-tr zhijie-tr-clickable" id="tr-<?php echo $auction?>">
                                        <td><?php echo $auction_basic_info['address']?></td>
                                        <td><?php echo $auction_basic_info['date_time']?></td>
                                        <td><?php echo $auction_basic_info['status']?></td>
                                        <td><?php echo "$".$auction_new_info['price']?></td>
                                        <td><?php echo $auction_new_info['calls']?></td>
                                    </tr>
                                <?php endif ;?>
                            <?php endforeach ; ?>
                        </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <p class="fw-light fst-italic">You have not yet added any events to your list. Please use the search bar above to find an event!</p>
                <?php endif ;?>
        </div>
    </div>

<?php // else : ?>
    <!-- <section class="jumbotron text-center bg-white">
        <div class="text-center">
            <h3>You must log in first. Redirecting...</h3>
        </div>
    </section> -->
    <?php 
        // sleep(3);
        // echo '<script>window.location = "'.home_url().'/login'.'" </script>';
    ?>
<?php // endif ;?>

<?php get_footer();