<?php

namespace App\Http\Controllers;

use Mail;

use Image;
use App\Helper;
use Carbon\Carbon;
use App\Models\Like;
use App\Models\User;
use App\Models\Blogs;
use App\Models\Media;
use Yabacon\Paystack;
use App\Models\States;
use App\Models\Reports;
use App\Models\Stories;
use App\Models\Updates;
use App\Models\Comments;
use App\Models\Deposits;
use App\Models\Products;
use App\Models\TaxRates;
use App\Models\Countries;
use App\Models\Purchases;
use App\Models\Referrals;
use App\Models\Categories;
use App\Models\StoryFonts;
use App\Models\Withdrawals;
use Illuminate\Support\Str;
use App\Events\NewPostEvent;
use App\Http\Resources\GiftPackageResource;
use App\Http\Resources\GiftResource;
use App\Http\Resources\SliderResource;
use App\Models\Transactions;
use Illuminate\Http\Request;
use App\Models\AdminSettings;
use App\Models\Gift;
use App\Models\GiftPackage;
use App\Models\Notifications;
use App\Models\Subscriptions;
use App\Models\ShopCategories;
use App\Models\PaymentGateways;
use Illuminate\Validation\Rule;
use App\Models\StoryBackgrounds;
use Illuminate\Support\Facades\DB;
use App\Notifications\PostRejected;
use App\Models\ReferralTransactions;
use App\Models\Slider;
use App\Models\VerificationRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;


class AdminController extends Controller
{
	use Traits\UserDelete, Traits\Functions;

	public function __construct(AdminSettings $settings)
	{
		$this->settings = $settings::first();
	}

	/**
	 * Show Dashboard section
	 *
	 * @return Response
	 */
	public function admin()
	{
		if (! auth()->user()->hasPermission('dashboard')) {
				return view('admin.unauthorized');
		}

		$dates = $this->generateDates(Carbon::parse()->startOfMonth(), Carbon::parse()->endOfMonth());

		$earningsChart = Transactions::selectRaw('DATE(`created_at`) as `date`')
			->selectRaw('SUM(`earning_net_admin`) as `total`')
			->where('created_at', '>', Carbon::parse()->startOfMonth())
			->where('created_at', '<', Carbon::parse()->endOfMonth())
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()
			->pluck('total', 'date');

		$dataChartQuery = $dates->merge($earningsChart);

		foreach ($dataChartQuery as $key => $value) {
			$allData[] = $value;
		}

		$dataChart = implode(',', $allData);

		$subscriptionsChart = Subscriptions::selectRaw('DATE(`created_at`) as `date`')
			->selectRaw('COUNT(`id`) as `total`')
			->where('created_at', '>', Carbon::parse()->startOfMonth())
			->where('created_at', '<', Carbon::parse()->endOfMonth())
			->groupBy('date')
			->orderBy('date', 'ASC')
			->get()
			->pluck('total', 'date');

		$subscriptionsChartQuery = $dates->merge($subscriptionsChart);

		foreach ($subscriptionsChartQuery as $key => $value) {
			$allDataSubscriptions[] = $value;
		}

		$dataChartSubscriptions = implode(',', $allDataSubscriptions);

		$totalUsers = User::selectRaw('COUNT(`id`) as `total`')->pluck('total')->first();
		$total_subscriptions = Subscriptions::selectRaw('COUNT(id) as total')->pluck('total')->first();
		$total_posts = Updates::selectRaw('COUNT(`id`) as `total`')->pluck('total')->first();

		$users = User::select(['id', 'username', 'avatar', 'name', 'status', 'date'])
			->orderBy('id','DESC')
			->take(4)
			->get();

		$subscriptions = Subscriptions::with(['subscriber:id,username,avatar,name', 'creator:id,username,name,plan'])
			->select(['id', 'user_id', 'stripe_price', 'created_at'])
			->orderBy('id','desc')
			->take(4)
			->get();

		$withdrawalsPaid = DB::table('withdrawals')
			->selectRaw('SUM(CASE WHEN MONTH(date_paid) = "'.Carbon::now()->subMonth()->month.'" THEN amount ELSE 0 END) AS lastMonth')
			->first();

		$revenues = Transactions::whereApproved('1')
		->selectRaw('SUM(earning_net_admin) AS totalRaisedFunds')
		->selectRaw('SUM(earning_net_user) AS totalUserRaisedFunds')
		->selectRaw('SUM(CASE WHEN created_at >= "'.Carbon::today().'" THEN earning_net_admin ELSE 0 END) AS today')
		->selectRaw('SUM(CASE WHEN created_at >= "'.Carbon::yesterday().'" AND created_at < "'.Carbon::today().'" THEN earning_net_admin ELSE 0 END) AS yesterday')
		->selectRaw('SUM(CASE WHEN created_at BETWEEN "'.Carbon::parse()->startOfWeek().'" AND "'.Carbon::parse()->endOfWeek().'" THEN earning_net_admin ELSE 0 END) AS week')
		->selectRaw('SUM(CASE WHEN created_at BETWEEN "'.Carbon::parse()->startOfWeek()->subWeek().'" AND "'.Carbon::parse()->subWeek()->endOfWeek().'" THEN earning_net_admin ELSE 0 END) AS lastWeek')
		->selectRaw('SUM(CASE WHEN created_at BETWEEN "'.Carbon::parse()->startOfMonth().'" AND "'.Carbon::parse()->endOfMonth().'" THEN earning_net_admin ELSE 0 END) AS month')
		->selectRaw('SUM(CASE WHEN created_at BETWEEN "'.Carbon::parse()->startOfMonth()->subMonth().'" AND "'.Carbon::parse()->subMonth()->endOfMonth().'" THEN earning_net_admin ELSE 0 END) AS lastMonth')
		->first();

		// Total Paid Withdrawals
		$totalPaidlastMonth = $withdrawalsPaid->lastMonth;

		// Today
		$stat_revenue_today = $revenues->today;
		// Yesterday
		$stat_revenue_yesterday = $revenues->yesterday;
		 // Week
	 	$stat_revenue_week = $revenues->week;
		// Last Week
		$stat_revenue_last_week = $revenues->lastWeek;
		 // Month
	 	$stat_revenue_month = $revenues->month;
		// Last Month
		$stat_revenue_last_month = $revenues->lastMonth;

		return view('admin.dashboard', [
			'dataChart' => $dataChart,
			'users' => $users,
			'totalUsers' => $totalUsers,
			'total_raised_funds' => $revenues->totalRaisedFunds,
			'total_funds' => $revenues->totalUserRaisedFunds + $revenues->totalRaisedFunds,
			'total_paid_funds' => $revenues->totalUserRaisedFunds,
			'totalPaidlastMonth' => $totalPaidlastMonth,
			'total_subscriptions' => $total_subscriptions,
			'subscriptions' => $subscriptions,
			'total_posts' => $total_posts,
			'stat_revenue_today' => $stat_revenue_today,
			'stat_revenue_yesterday' => $stat_revenue_yesterday,
			'stat_revenue_week' => $stat_revenue_week,
			'stat_revenue_last_week' => $stat_revenue_last_week,
			'stat_revenue_month' => $stat_revenue_month,
			'stat_revenue_last_month' => $stat_revenue_last_month,
			'label' => Helper::formatMonth(),
			'dataChartSubscriptions' => $dataChartSubscriptions
		]);

	}//<--- END METHOD

	/**
	 * Show Members section
	 *
	 * @return Response
	 */
	 public function index(Request $request)
	 {
		 $search = $request->input('q');
		 $sort  = $request->input('sort');

		 if ($search != '' && strlen( $search ) > 2) {
			 $data = User::where('name', 'LIKE', '%'.$search.'%')
			 ->orWhere('username', 'LIKE', '%'.$search.'%')
			 ->orWhere('email', 'LIKE', '%'.$search.'%')
			 ->orderBy('id','desc')->paginate(20);
		 } else {
			 $data = User::orderBy('id','desc')->paginate(20);
		 }

		 if (request('sort') == 'admins') {
			 $data = User::whereRole('admin')->orderBy('id','desc')->paginate(20);
		 }

		 if (request('sort') == 'creators') {
			 $data = User::where('verified_id', 'yes')->orderBy('id','desc')->paginate(20);
		 }

		 if (request('sort') == 'email_pending') {
			 $data = User::whereStatus('pending')->orderBy('id','desc')->paginate(20);
		 }

		 if (request('sort') == 'balance') {
			$data = User::orderBy('balance','desc')->paginate(20);
		}

		if (request('sort') == 'wallet') {
			$data = User::orderBy('wallet','desc')->paginate(20);
		}

		 return view('admin.members', ['data' => $data, 'query' => $search, 'sort' => $sort]);
	 }

	public function edit($id)
	{
		$user = User::findOrFail($id);

		if ($user->id == 1 || $user->id == auth()->user()->id) {
			\Session::flash('info_message', __('admin.user_no_edit'));
			return redirect('panel/admin/members');
		}
    	return view('admin.edit-member')->withUser($user);

	}//<--- End Method

	public function update($id, Request $request)
	{
		$request->validate([
			'email' => 'required|email|max:255|unique:users,email,'.$id,
		]);

    $user = User::findOrFail($id);

		 if ($request->featured == 'yes' && $user->featured == 'no') {
			 $featured_date = Carbon::now();
		 } else {
			 $featured_date = $user->featured_date;
		 }

		 if ($request->featured == 'no' && $user->featured == 'yes') {
			 $featured_date = null;
		 }

		$user->email = $request->email;
		$user->verified_id = $request->verified;
		$user->status = $request->status;
		$user->custom_fee = $request->custom_fee ?? 0;
		$user->featured = $request->featured ?? 'no';
		$user->featured_date = $featured_date;
		$user->balance = $request->balance;
		$user->wallet = $request->wallet;
    $user->save();

    \Session::flash('success_message', __('admin.success_update'));

    return redirect('panel/admin/members');

	}//<--- End Method

	public function destroy($id)
	{
		// Find User
    $user = User::findOrFail($id);

  	if ($user->isSuperAdmin() || $user->id == auth()->user()->id) {
			return redirect('panel/admin/members');
		}

		$this->deleteUser($id);

		return redirect('panel/admin/members');

    }//<--- End Method

	public function settings()
	{
		$genders = explode(',', $this->settings->genders);

		return view('admin.settings', ['genders' => $genders]);
	}//<--- END METHOD

	public function saveSettings(Request $request)
	{
		// The referral system cannot be activated if your commission fee equals 0
		if ($this->settings->fee_commission == 0 && $request->referral_system == 'on') {
			return back()->withErrors([
				'errors' => __('general.error_active_system_referrals'),
			]);
		}

		// Verify captcha API keys exists
		if ($request->captcha || $request->captcha_contact ) {
			if (config('captcha.sitekey') == '' && config('captcha.secret') == '') {
				return back()->withErrors([
					'errors' => __('general.error_active_captcha'),
				]);
			}
		}

		$messages = [
			'genders.required' => __('general.genders_required'),
		];

		$request->validate([
			'title'            => 'required',
			'email_admin'      => 'required',
			'link_terms'       => 'required|url',
			'link_privacy'     => 'required|url',
			'link_cookies'     => 'required|url',
			'genders'          => 'required',
		], $messages);

		if (isset($request->genders)) {
				$genders = implode( ',', $request->genders);
			}

		$sql                      = $this->settings;
		$sql->title               = $request->title;
		$sql->email_admin         = $request->email_admin;
		$sql->link_terms         = $request->link_terms;
		$sql->link_privacy         = $request->link_privacy;
		$sql->link_cookies         = $request->link_cookies;
		$sql->date_format         = $request->date_format;
		$sql->captcha                = $request->captcha ?? 'off';
		$sql->email_verification = $request->email_verification ?? false;
		$sql->registration_active = $request->registration_active ?? false;
		$sql->disable_login_register_email = $request->disable_login_register_email ?? false;
		$sql->account_verification = $request->account_verification ?? false;
		$sql->show_counter = $request->show_counter ?? 'off';
		$sql->widget_creators_featured = $request->widget_creators_featured ?? 'off';
		$sql->requests_verify_account = $request->requests_verify_account ?? 'off';
		$sql->hide_admin_profile = $request->hide_admin_profile ?? 'off';
		$sql->earnings_simulator = $request->earnings_simulator ?? 'off';
		$sql->watermark = $request->watermark ?? 'off';
		$sql->alert_adult = $request->alert_adult ?? 'off';
		$sql->genders = $genders;
		$sql->who_can_see_content = $request->who_can_see_content;
		$sql->users_can_edit_post = $request->users_can_edit_post ?? 'off';
		$sql->disable_banner_cookies = $request->disable_banner_cookies ?? 'off';
		$sql->captcha_contact = $request->captcha_contact ?? 'off';
		$sql->disable_tips = $request->disable_tips ?? 'off';
		$sql->watermark_on_videos = $request->watermark_on_videos ?? 'off';
		$sql->referral_system = $request->referral_system ?? 'off';
		$sql->video_encoding = $request->video_encoding ?? 'off';
		$sql->disable_contact = $request->disable_contact;
		$sql->disable_new_post_notification = $request->disable_new_post_notification;
		$sql->disable_search_creators = $request->disable_search_creators;
		$sql->search_creators_genders = $request->search_creators_genders;
		$sql->generate_qr_code = $request->generate_qr_code;
		$sql->autofollow_admin = $request->autofollow_admin;
		$sql->allow_zip_files = $request->allow_zip_files;
		$sql->zip_verification_creator = $request->zip_verification_creator;
		$sql->save();

		// Default locale
		Helper::envUpdate('DEFAULT_LOCALE', $request->default_language);

		// App Name
		Helper::envUpdate('APP_NAME', ' "'.$request->title.'" ', true);

		// APP Debug
		$path = base_path('.env');

		if (env('APP_DEBUG') == true) {
			$APP_DEBUG = 'APP_DEBUG=true';
		} else {
			$APP_DEBUG = 'APP_DEBUG=false';
		}

		if (file_exists($path)) {
			file_put_contents($path, str_replace(
					$APP_DEBUG, 'APP_DEBUG=' . $request->app_debug, file_get_contents($path)
			));
		}

		return redirect('panel/admin/settings')->withSuccessMessage(__('admin.success_update'));

	}//<--- END METHOD

	public function settingsLimits()
	{
		return view('admin.limits')->withSettings($this->settings);
	}//<--- END METHOD

	public function saveSettingsLimits(Request $request)
	{

		$sql                     = AdminSettings::first();
		$sql->auto_approve_post  = $request->auto_approve_post;
		$sql->file_size_allowed  = $request->file_size_allowed;
		$sql->file_size_allowed_verify_account  = $request->file_size_allowed_verify_account;
		$sql->update_length      = $request->update_length;
		$sql->story_length      = $request->story_length;
		$sql->comment_length     = $request->comment_length;
		$sql->number_posts_show  = $request->number_posts_show;
		$sql->number_comments_show = $request->number_comments_show;
		$sql->maximum_files_post = $request->maximum_files_post;
		$sql->maximum_files_msg = $request->maximum_files_msg;
		$sql->limit_categories = $request->limit_categories;
		$sql->save();

		\Session::flash('success_message', __('admin.success_update'));

    	return redirect('panel/admin/settings/limits');

	}//<--- END METHOD
	public function settingsSlider()
	{
        $results = Slider::all(); // Obtén las imágenes del slider desde la base de datos
        $sliders = SliderResource::collection($results);
		return view('admin.slider',['sliders'=>$sliders])->withSettings($this->settings);
	}
    public function settingsGift()
	{
        $results = Gift::all(); // Obtén las imágenes del slider desde la base de datos
        $gifts = GiftResource::collection($results);
		return view('admin.gift',['gifts'=>$gifts])->withSettings($this->settings);
	}
    public function settingsGiftPackage()
	{
        $results = GiftPackage::all(); // Obtén las imágenes del slider desde la base de datos
        $giftsPackage = GiftPackageResource::collection($results);
		return view('admin.gift_package',['gifts_packages'=>$giftsPackage])->withSettings($this->settings);
	}

    //<--- END METHOD
    public function saveSettingsSlider(Request $request)
	{}
    public function saveSettingsGift(Request $request)
	{}//<--- END METHOD
	public function maintenanceMode(Request $request)
	{
		$strRandom = str_random(50);

		if (auth()->user()->isSuperAdmin() && $request->maintenance_mode) {
			\Artisan::call('down', [
				'--secret' => $strRandom
			]);
		} elseif (auth()->user()->isSuperAdmin() && ! $request->maintenance_mode) {
			\Artisan::call('up');
		}

		$this->settings->maintenance_mode = $request->maintenance_mode;
		$this->settings->save();

		if ($request->maintenance_mode) {
			return redirect($strRandom)
			->withSuccessMessage(__('admin.maintenance_mode_on'));
		} else {
			return redirect('panel/admin/maintenance/mode')
			->withSuccessMessage(__('admin.maintenance_mode_off'));
		}

	}//<--- END METHOD

	public function profiles_social()
	{
		return view('admin.profiles-social')->withSettings($this->settings);
	}//<--- End Method

	public function update_profiles_social(Request $request)
	{
		$sql = AdminSettings::find(1);

		$rules = array(
						'facebook'   => 'url',
            'twitter'    => 'url',
            'instagram' => 'url',
						'linkedin' => 'url',
            'youtube'    => 'url',
						'pinterest'  => 'url',
						'tiktok'     => 'url',
						'telegram'   => 'url',
						'reddit'     => 'url',
						'snapchat'   => 'url',
						'github'   => 'url',
        );

		$this->validate($request, $rules);

	  $sql->facebook  = $request->facebook;
		$sql->twitter   = $request->twitter;
		$sql->instagram = $request->instagram;
		$sql->linkedin  = $request->linkedin;
		$sql->youtube   = $request->youtube;
		$sql->pinterest = $request->pinterest;
		$sql->tiktok    = $request->tiktok;
		$sql->telegram  = $request->telegram;
		$sql->reddit    = $request->reddit;
		$sql->snapchat  = $request->snapchat;
		$sql->github    = $request->github;

		$sql->save();

	    \Session::flash('success_message', __('admin.success_update'));

	    return redirect('panel/admin/profiles-social');
	}//<--- End Method

	public function subscriptions()
	{
		$data = Subscriptions::orderBy('id','DESC')->paginate(50);

		return view('admin.subscriptions', ['data' => $data]);

	}//<--- End Method

	public function transactions(Request $request)
	{
		$query = $request->input('q');

		if ($query != '' && strlen($query) > 2) {
			$data = Transactions::where('txn_id', 'LIKE', '%'.$query.'%')->orderBy('id','DESC')->paginate(50);
		} else {
			$data = Transactions::orderBy('id','DESC')->paginate(50);
		}

		return view('admin.transactions', ['data' => $data]);
	}//<--- End Method

	public function cancelTransaction($id)
	{
		$transaction = Transactions::whereId($id)->whereApproved('1')->firstOrFail();

		// Cancel subscription
		$subscription = $transaction->subscription();

		switch ($transaction->payment_gateway) {
			case 'Stripe':

			if (isset($subscription)) {
				$stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
				$stripe->subscriptions->cancel($subscription->stripe_id, []);
			}

			break;

			case 'Paystack':
			if (isset($subscription)) {
				$payment = PaymentGateways::whereId(4)->whereName('Paystack')->whereEnabled(1)->first();

				$curl = curl_init();

				curl_setopt_array($curl, array(
					CURLOPT_URL => "https://api.paystack.co/subscription/".$id,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "GET",
					CURLOPT_HTTPHEADER => array(
						"Authorization: Bearer ".$payment->key_secret,
						"Cache-Control: no-cache",
					),
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);
				curl_close($curl);

				if ($err) {
					throw new \Exception("cURL Error #:" . $err);
				} else {
					 $result = json_decode($response);
				}

				// initiate the Library's Paystack Object
				$paystack = new Paystack($payment->key_secret);

				$paystack->subscription->disable([
									'code'=> $subscription->subscription_id,
									'token'=> $result->data->email_token
								]);
				}

			break;
		}

		if (isset($subscription)) {
			$subscription->delete();
		}

		// Subtract user earnings
		User::whereId($transaction->subscribed)->decrement('balance', $transaction->earning_net_user);

		// Change status transaction to canceled
		$transaction->approved = '2';
		$transaction->earning_net_user = 0;
		$transaction->earning_net_admin = 0;
		$transaction->save();

		\Session::flash('success_message', __('admin.success_update'));

    return redirect('panel/admin/transactions');
	}


	public function payments()
	{
		$stripeConnectCountries = explode(',', $this->settings->stripe_connect_countries);

		return view('admin.payments-settings')->withStripeConnectCountries($stripeConnectCountries);
	}//<--- End Method

	public function savePayments(Request $request)
	{
		$sql = AdminSettings::first();

		// The referral system cannot be activated if your commission fee equals 0
		if ($request->fee_commission == 0 && $this->settings->referral_system == 'on') {
			return back()->withErrors([
				'errors' => __('general.error_fee_commission_zero'),
			]);
		}

		$messages = [
			'stripe_connect_countries.required' => __('validation.required', ['attribute' => __('general.stripe_connect_countries')])
		];

		$rules = [
						'currency_code' => 'required|alpha',
						'currency_symbol' => 'required',
						'min_subscription_amount' => 'required|numeric|min:1',
						'max_subscription_amount' => 'required|numeric|min:1',
						'stripe_connect_countries' => Rule::requiredIf($request->stripe_connect == 1)
        ];

		$this->validate($request, $rules, $messages);

		if (isset($request->stripe_connect_countries)) {
				$stripeConnectCountries = implode( ',', $request->stripe_connect_countries);
			}

		$sql->currency_symbol  = $request->currency_symbol;
		$sql->currency_code    = strtoupper($request->currency_code);
		$sql->currency_position = $request->currency_position;
		$sql->min_subscription_amount   = $request->min_subscription_amount;
		$sql->max_subscription_amount   = $request->max_subscription_amount;
		$sql->min_tip_amount   = $request->min_tip_amount;
		$sql->max_tip_amount   = $request->max_tip_amount;
		$sql->min_ppv_amount   = $request->min_ppv_amount;
		$sql->max_ppv_amount   = $request->max_ppv_amount;
		$sql->min_deposits_amount   = $request->min_deposits_amount;
		$sql->max_deposits_amount   = $request->max_deposits_amount;
		$sql->fee_commission       = $request->fee_commission;
		$sql->percentage_referred  = $request->percentage_referred;
		$sql->referral_transaction_limit  = $request->referral_transaction_limit;
		$sql->amount_min_withdrawal    = $request->amount_min_withdrawal;
		$sql->amount_max_withdrawal    = $request->amount_max_withdrawal;
		$sql->specific_day_payment_withdrawals = $request->specific_day_payment_withdrawals;
		$sql->days_process_withdrawals = $request->days_process_withdrawals;
		$sql->type_withdrawals = $request->type_withdrawals;
		$sql->payout_method_paypal = $request->payout_method_paypal;
		$sql->payout_method_payoneer = $request->payout_method_payoneer;
		$sql->payout_method_zelle = $request->payout_method_zelle;
		$sql->payout_method_western_union = $request->payout_method_western_union;
		$sql->payout_method_bank = $request->payout_method_bank;
		$sql->decimal_format           = $request->decimal_format;
		$sql->disable_wallet = $request->disable_wallet;
		$sql->tax_on_wallet = $request->tax_on_wallet;
		$sql->wallet_format = $request->wallet_format;
		$sql->stripe_connect = $request->stripe_connect;
		$sql->stripe_connect_countries = $stripeConnectCountries ?? null;

		$sql->save();

	    \Session::flash('success_message', __('admin.success_update'));

	    return redirect('panel/admin/payments');
	}//<--- End Method

	public function withdrawals()
	{
		$data = Withdrawals::orderBy('id','DESC')->paginate(50);
		return view('admin.withdrawals', ['data' => $data]);
	}//<--- End Method

	public function withdrawalsView($id)
	{
		$data = Withdrawals::findOrFail($id);
		return view('admin.withdrawal-view', ['data' => $data]);
	}//<--- End Method

	public function withdrawalsPaid(Request $request)
	{
		$data = Withdrawals::findOrFail($request->id);

		$user = $data->user();

		$data->status    = 'paid';
		$data->date_paid = Carbon::now();
		$data->save();

		//<------ Send Email to User ---------->>>
		$amount       = Helper::amountWithoutFormat($data->amount).' '.$this->settings->currency_code;
		$sender       = $this->settings->email_no_reply;
	  	$titleSite    = $this->settings->title;
		$fullNameUser = $user->name;
		$_emailUser   = $user->email;

		Mail::send('emails.withdrawal-processed', array(
					'amount'     => $amount,
					'title_site' => $titleSite,
					'fullname'   => $fullNameUser
		),
			function($message) use ($sender, $fullNameUser, $titleSite, $_emailUser)
				{
				    $message->from($sender, $titleSite)
									  ->to($_emailUser, $fullNameUser)
										->subject( __('general.withdrawal_processed').' - '.$titleSite );
				});
			//<------ Send Email to User ---------->>>

		return redirect('panel/admin/withdrawals');

	}//<--- End Method


	// START
	public function categories()
	{
		$categories      = Categories::orderBy('name')->get();
		$totalCategories = count( $categories );

		return view('admin.categories', compact( 'categories', 'totalCategories' ));
	}//<--- END METHOD

	public function addCategories()
	{
		return view('admin.add-categories');
	}//<--- END METHOD

	public function storeCategories(Request $request) {

		$temp            = '/temp/'; // Temp
	  $path            = 'img-category/'; // Path General

		Validator::extend('ascii_only', function($attribute, $value, $parameters){
    		return !preg_match('/[^x00-x7F\-]/i', $value);
		});

		$rules = array(
          'name'        => 'required',
	        'slug'        => 'required|ascii_only|unique:categories',
	        'thumbnail'   => 'required|mimes:jpg,gif,png,jpe,jpeg|dimensions:min_width=30,min_height=30',
        );

		$this->validate($request, $rules);

		if( $request->hasFile('thumbnail') ) {

		$extension       = $request->file('thumbnail')->getClientOriginalExtension();
		$type_mime_image = $request->file('thumbnail')->getMimeType();
		$sizeFile        = $request->file('thumbnail')->getSize();
		$thumbnail       = $request->slug.'-'.Str::random(32).'.'.$extension;

		if( $request->file('thumbnail')->move($temp, $thumbnail) ) {

			$image = Image::make($temp.$thumbnail);

			\File::copy($temp.$thumbnail, $path.$thumbnail);
			\File::delete($temp.$thumbnail);
			}// End File
		} // HasFile

else {
	$thumbnail = '';
}

		$sql              = New Categories;
		$sql->name        = $request->name;
		$sql->slug        = $request->slug;
		$sql->keywords    = $request->keywords;
		$sql->description = $request->description;
		$sql->mode        = $request->mode ?? 'off';
		$sql->image       = $thumbnail;
		$sql->save();

		\Session::flash('success_message', __('admin.success_add_category'));

    	return redirect('panel/admin/categories');

	}//<--- END METHOD

	public function editCategories($id) {

		$categories = Categories::find($id);

		return view('admin.edit-categories')->with('categories', $categories);

	}//<--- END METHOD

	public function updateCategories(Request $request)
	{
		$categories        = Categories::find($request->id);
		$temp            = '/temp/'; // Temp
	  $path            = 'img-category/'; // Path General

	  if(!isset($categories)) {
			return redirect('panel/admin/categories');
		}

		Validator::extend('ascii_only', function($attribute, $value, $parameters){
    		return !preg_match('/[^x00-x7F\-]/i', $value);
		});

		$rules = array(
          'name'        => 'required',
	        'slug'        => 'required|ascii_only|unique:categories,slug,'.$request->id,
	        'thumbnail'   => 'mimes:jpg,gif,png,jpe,jpeg|dimensions:min_width=30,min_height=30',
	     );

		$this->validate($request, $rules);

		if($request->hasFile('thumbnail')) {

		$extension        = $request->file('thumbnail')->getClientOriginalExtension();
		$type_mime_image   = $request->file('thumbnail')->getMimeType();
		$sizeFile         = $request->file('thumbnail')->getSize();
		$thumbnail        = $request->slug.'-'.Str::random(32).'.'.$extension;

		if($request->file('thumbnail')->move($temp, $thumbnail)) {

			$image = Image::make($temp.$thumbnail);

			\File::copy($temp.$thumbnail, $path.$thumbnail);
			\File::delete($temp.$thumbnail);

			// Delete Old Image
			\File::delete($path.$categories->thumbnail);

			}// End File
		} // HasFile
		else {
			$thumbnail = $categories->image;
		}

		// UPDATE CATEGORY
		$categories->name   = $request->name;
		$categories->slug   = $request->slug;
		$categories->keywords    = $request->keywords;
		$categories->description = $request->description;
		$categories->mode   = $request->mode ?? 'off';
		$categories->image  = $thumbnail;
		$categories->save();

		\Session::flash('success_message', __('general.success_update'));
		return redirect('panel/admin/categories');

	}//<--- END METHOD

	public function deleteCategories($id)
	{

			$categories   = Categories::findOrFail($id);
			$thumbnail    = 'img-category/'.$categories->image; // Path General

			$userCategory = User::where('categories_id', $id)->update(['categories_id' => 0]);

			// Delete Category
			$categories->delete();

			// Delete Thumbnail
			if ( \File::exists($thumbnail) ) {
				\File::delete($thumbnail);
			}//<--- IF FILE EXISTS

			return redirect('panel/admin/categories');
	}//<--- END METHOD

	public function posts(Request $request)
	{
		$data = Updates::orderBy('id','desc')->paginate(20);
		$sort  = $request->input('sort');

		if (request('sort') == 'pending') {
			$data = Updates::whereStatus('pending')->orderBy('id','desc')->paginate(20);
		}

		return view('admin.posts', ['data' => $data, 'sort' => $sort]);
	}

	public function deletePost(Request $request)
	{
	  	$sql       = Updates::findOrFail($request->id);
		$path      = config('path.images');
		$pathVideo = config('path.videos');
		$pathMusic = config('path.music');
		$pathFile  = config('path.files');

		if ($sql->status == 'pending') {
			try {
				$sql->user()->notify(new PostRejected($sql)); // Send email to user
			} catch (\Exception $e) {
				\Log::info($e->getMessage());
			}
		}

		$files = Media::whereUpdatesId($sql->id)->get();

		foreach ($files as $media) {

      if ($media->image) {
        Storage::delete($path.$media->image);
        $media->delete();
      }

      if ($media->video) {
        Storage::delete($pathVideo.$media->video);
				Storage::delete($pathVideo.$media->video_poster);
        $media->delete();
      }

      if ($media->music) {
        Storage::delete($pathMusic.$media->music);
        $media->delete();
      }

      if ($media->file) {
        Storage::delete($pathFile.$media->file);
        $media->delete();
      }

		if ($media->video_embed) {
        $media->delete();
      }

    }

		// Delete Reports
		$reports = Reports::where('report_id', $request->id)->where('type','update')->get();

		if(isset($reports)){
			foreach($reports as $report){
				$report->delete();
			}
		}

		// Delete Notifications
		Notifications::where('target', $request->id)
			->where('type', '2')
			->orWhere('target', $request->id)
			->where('type', '3')
			->orWhere('target', $request->id)
			->where('type', '6')
			->orWhere('target', $request->id)
			->where('type', '7')
			->orWhere('target', $request->id)
			->where('type', '8')
			->orWhere('target', $request->id)
			->where('type', '9')
			->delete();

			// Delete Likes Comments
			foreach ($sql->comments()->get() as $key) {
				$key->likes()->delete();
			}

			// Delete Comments
			$sql->comments()->delete();

			// Delete Replies
			$sql->replies()->delete();

			// Delete likes
			Like::where('updates_id', $request->id)->delete();

			$sql->delete();

		return back();

	}//<--- End Method

	public function reports()
	{
		$data = Reports::orderBy('id','desc')->get();
		return view('admin.reports')->withData($data);
	}

	public function deleteReport(Request $request) {

		$report = Reports::findOrFail($request->id);
		$report->delete();
		return redirect('panel/admin/reports');

	}//<--- END METHOD

	public function paymentsGateways($id)
	{
		$data = PaymentGateways::findOrFail($id);
		$name = ucfirst($data->name);

		return view('admin.'.str_slug($name).'-settings')->withData($data);
	}//<--- End Method

	public function savePaymentsGateways($id, Request $request)
	{
		$data  = PaymentGateways::findOrFail($id);
		$input = $_POST;

		// Sandbox off
		if (! $request->sandbox) {
		   $input['sandbox'] = 'false';
		}

		// Enabled off
		if (! $request->enabled) {
		   $input['enabled'] = '0';
		}

		$this->validate($request, [
            'email' => 'email',
        ]);

		$data->fill($input)->save();

		// Set PayPal Keys on .env file
		if ($data->name == 'PayPal') {
			if (! $request->sandbox) {
				Helper::envUpdate('PAYPAL_MODE', 'live');
				Helper::envUpdate('PAYPAL_LIVE_CLIENT_ID', $input['key']);
				Helper::envUpdate('PAYPAL_LIVE_CLIENT_SECRET', $input['key_secret']);
			} else {
				Helper::envUpdate('PAYPAL_MODE', 'sandbox');
				Helper::envUpdate('PAYPAL_SANDBOX_CLIENT_ID', $input['key']);
				Helper::envUpdate('PAYPAL_SANDBOX_CLIENT_SECRET', $input['key_secret']);
			}

			Helper::envUpdate('PAYPAL_WEBHOOK_ID', $input['webhook_secret']);

		}// PayPal

		// Set Keys on .env file
		if ($data->name == 'Stripe') {
			Helper::envUpdate('STRIPE_KEY', $input['key']);
			Helper::envUpdate('STRIPE_SECRET', $input['key_secret']);
			Helper::envUpdate('STRIPE_WEBHOOK_SECRET', $input['webhook_secret']);
		}

		// Set Keys on .env file
		if ($data->name == 'Flutterwave') {
			Helper::envUpdate('FLW_PUBLIC_KEY', $input['key']);
			Helper::envUpdate('FLW_SECRET_KEY', $input['key_secret']);
		}

    return back()->withSuccessMessage(__('admin.success_update'));
	}//<--- End Method

	public function theme()
	{
		return view('admin.theme');

	}//<--- End method

	public function themeStore(Request $request)
	{
		$temp  = '/temp/'; // Temp
	  	$path  = 'img/'; // Path
		$pathAvatar  = config('path.avatar'); // Path

		$rules = array(
          'logo'   => 'mimes:png,svg',
					'logo_blue'   => 'mimes:png,svg',
					'favicon'   => 'mimes:png,svg',
					'color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
				  'navbar_background_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
				  'navbar_text_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
				  'footer_background_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
				  'footer_text_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/']
        );

		$this->validate($request, $rules);

		//======= LOGO
		if( $request->hasFile('logo') )	{

		$extension = $request->file('logo')->getClientOriginalExtension();
		$file      = 'logo-'.time().'.'.$extension;

		if ($request->file('logo')->move($temp, $file)) {
			\File::copy($temp.$file, $path.$file);
			\File::delete($temp.$file);
			// Delete old
			\File::delete($path.$this->settings->logo);
			}// End File

			$this->settings->logo = $file;
			$this->settings->save();

		} // HasFile

		//======= LOGO BLUE
		if( $request->hasFile('logo_2') ) {

		$extension = $request->file('logo_2')->getClientOriginalExtension();
		$file      = 'logo_2-'.time().'.'.$extension;

		if ($request->file('logo_2')->move($temp, $file)) {
			\File::copy($temp.$file, $path.$file);
			\File::delete($temp.$file);
			// Delete old
			\File::delete($path.$this->settings->logo_2);
			}// End File

			$this->settings->logo_2 = $file;
			$this->settings->save();

		} // HasFile

		//======== FAVICON
		if($request->hasFile('favicon') )	{

		$extension  = $request->file('favicon')->getClientOriginalExtension();
		$file       = 'favicon-'.time().'.'.$extension;

		if ($request->file('favicon')->move($temp, $file)) {
			\File::copy($temp.$file, $path.$file);
			\File::delete($temp.$file);
			// Delete old
			\File::delete($path.$this->settings->favicon);
			}// End File

			$this->settings->favicon = $file;
			$this->settings->save();

		} // HasFile

		//======== Image Header
		if ($request->hasFile('index_image_top') )	{

		$extension  = $request->file('index_image_top')->getClientOriginalExtension();
		$file       = 'home_index-'.time().'.'.$extension;

		if ($request->file('index_image_top')->move($temp, $file)) {
			\File::copy($temp.$file, $path.$file);
			\File::delete($temp.$file);
			// Delete old
			\File::delete($path.$this->settings->home_index);
			}// End File

			$this->settings->home_index = $file;
			$this->settings->save();

		} // HasFile

		//======== Background
		if ($request->hasFile('background') )	{

		$extension  = $request->file('background')->getClientOriginalExtension();
		$file       = 'background-'.time().'.'.$extension;

		if ($request->file('background')->move($temp, $file)) {
			\File::copy($temp.$file, $path.$file);
			\File::delete($temp.$file);
			// Delete old
			\File::delete($path.$this->settings->background);
			}// End File

			$this->settings->bg_gradient = $file;
			$this->settings->save();

		} // HasFile

		//======== Image on index 1
		if($request->hasFile('image_index_1') )	{

		$extension  = $request->file('image_index_1')->getClientOriginalExtension();
		$file       = 'image_index_1-'.time().'.'.$extension;

		if ($request->file('image_index_1')->move($temp, $file)) {
			\File::copy($temp.$file, $path.$file);
			\File::delete($temp.$file);
			// Delete old
			\File::delete($path.$this->settings->img_1);
			}// End File

			$this->settings->img_1 = $file;
			$this->settings->save();

		} // HasFile

		//======== Image on index 2
		if($request->hasFile('image_index_2') )	{

		$extension  = $request->file('image_index_2')->getClientOriginalExtension();
		$file       = 'image_index_2-'.time().'.'.$extension;

		if ($request->file('image_index_2')->move($temp, $file)) {
			\File::copy($temp.$file, $path.$file);
			\File::delete($temp.$file);
			// Delete old
			\File::delete($path.$this->settings->img_2);
			}// End File

			$this->settings->img_2 = $file;
			$this->settings->save();

		} // HasFile

		//======== Image on index 3
		if($request->hasFile('image_index_3') )	{

		$extension  = $request->file('image_index_3')->getClientOriginalExtension();
		$file       = 'image_index_3-'.time().'.'.$extension;

		if ($request->file('image_index_3')->move($temp, $file)) {
			\File::copy($temp.$file, $path.$file);
			\File::delete($temp.$file);
			// Delete old
			\File::delete($path.$this->settings->img_3);
			}// End File

			$this->settings->img_3 = $file;
			$this->settings->save();

		} // HasFile

		//======== Image on index 4
		if($request->hasFile('image_index_4') )	{

		$extension  = $request->file('image_index_4')->getClientOriginalExtension();
		$file       = 'image_index_4-'.time().'.'.$extension;

		if ($request->file('image_index_4')->move($temp, $file)) {
			\File::copy($temp.$file, $path.$file);
			\File::delete($temp.$file);
			// Delete old
			\File::delete($path.$this->settings->img_4);
			}// End File

			$this->settings->img_4 = $file;
			$this->settings->save();

		} // HasFile

		//======== Avatar
		if ($request->hasFile('avatar')) {

			$extension  = $request->file('avatar')->getClientOriginalExtension();
			$file       = 'default-'.time().'.'.$extension;

		$imgAvatar  = Image::make($request->file('avatar'))->fit(200, 200, function ($constraint) {
			$constraint->aspectRatio();
			$constraint->upsize();
		})->encode($extension);

		// Copy folder
		Storage::put($pathAvatar.$file, $imgAvatar, 'public');

		// Update Avatar all users
		User::where('avatar', $this->settings->avatar)->update([
					'avatar' => $file
				]);

		// Delete old Avatar
		Storage::delete(config('path.avatar').$this->settings->avatar);

			$this->settings->avatar = $file;
			$this->settings->save();
		} // HasFile

		//======== Cover
		if ($request->hasFile('cover_default')) {

			$pathCover = config('path.cover');
			$extension  = $request->file('cover_default')->getClientOriginalExtension();
			$file       = 'cover_default-'.time().'.'.$extension;

		$request->file('cover_default')->storePubliclyAs($pathCover, $file);

		// Update Cover all users
		User::where('cover', $this->settings->cover_default)
		->orWhere('cover', '')
		->update([
					'cover' => $file
				]);

		// Delete old Avatar
		Storage::delete($pathCover.$this->settings->cover_default);

			$this->settings->cover_default = $file;
			$this->settings->save();
		} // HasFile

		// Update Color Default, and Button style
		$this->settings->whereId(1)
			->update([
				'home_style' => $request->get('home_style'),
				'color_default' => $request->get('color'),
				'navbar_background_color' => $request->get('navbar_background_color'),
				'navbar_text_color' => $request->get('navbar_text_color'),
				'footer_background_color' => $request->get('footer_background_color'),
				'footer_text_color' => $request->get('footer_text_color'),
				'button_style' => $request->get('button_style')
			]);


		\Artisan::call('cache:clear');
		\Artisan::call('view:clear');

		return redirect('panel/admin/theme')
			 ->with('success_message', __('admin.success_update'));

	}//<--- End method

	// Google
	public function google()
	{
		return view('admin.google');
	}//<--- END METHOD

	public function update_google(Request $request)
	{
		$sql = $this->settings;
		$sql->google_analytics = $request->google_analytics;
		$sql->save();

		foreach ($request->except(['_token']) as $key => $value) {
			Helper::envUpdate($key, $value);
		}

		\Session::flash('success_message', __('admin.success_update'));

	    return redirect('panel/admin/google');
	}//<--- End Method

	// Verification Requests
	public function memberVerification()
	{
		$data = VerificationRequests::orderBy('id','desc')->paginate(30);
		return view('admin.verification')->withData($data);
	}

	// Verification Requests Send
	public function memberVerificationSend($action, $id, $user)
	{
			$member = User::find($user);
			$pathImage = config('path.verification');

			if (! isset($member)) {
				$sql = VerificationRequests::findOrFail($id);
				// Delete Image
				Storage::delete($pathImage.$sql->image);
				// Delete Form W-9
				Storage::delete($pathImage.$sql->form_w9);
				$sql->delete();

				\Session::flash('success_message', __('admin.success_update'));
				return redirect('panel/admin/verification/members');
			}

			// Data Email Send
			$sender       = $this->settings->email_no_reply;
		  	$titleSite    = $this->settings->title;
			$fullNameUser = $member->name;
			$emailUser   = $member->email;

		if ($action == 'approve') {
			$sql = VerificationRequests::whereId($id)->whereUserId($user)->whereStatus('pending')->firstOrFail();
			$sql->status = 'approved';
			$sql->save();

			// Update status verify of user
			$member->verified_id = 'yes';
			$member->save();

			// Send Notification
			Notifications::send($member->id, $member->id, 18, $member->id);

			try {
				//<------ Send Email to User ---------->>>
			Mail::send('emails.account_verification', array(
				'body' => __('general.body_account_verification_approved'),
				'title_site' => $titleSite,
				'fullname'   => $fullNameUser
			),
				function($message) use ($sender, $fullNameUser, $titleSite, $emailUser)
					{
					    $message->from($sender, $titleSite)
										  ->to($emailUser, $fullNameUser)
											->subject(__('general.account_verification_approved').' - '.$titleSite);
					});
				//<------ End Send Email to User ---------->>>

				\Session::flash('success_message', __('admin.success_update'));
			   return redirect('panel/admin/verification/members');

			} catch (\Exception $e) {}

		} elseif ($action == 'delete') {
			$sql = VerificationRequests::findOrFail($id);

			// Delete Image
			Storage::delete($pathImage.$sql->image);

			// Delete Form W-9
			Storage::delete($pathImage.$sql->form_w9);

			$sql->delete();

			// Update status verify of user
			$member->verified_id = 'reject';
			$member->save();

			// Send Notification
			Notifications::send($member->id, $member->id, 19, $member->id);

			try {
			//<------ Send Email to User ---------->>>
			Mail::send('emails.account_verification', array(
				'body' => __('general.body_account_verification_reject'),
				'title_site' => $titleSite,
				'fullname'   => $fullNameUser
			),
				function($message) use ($sender, $fullNameUser, $titleSite, $emailUser)
					{
					    $message->from($sender, $titleSite)
										  ->to($emailUser, $fullNameUser)
											->subject(__('general.account_verification_not_approved').' - '.$titleSite);
					});
				//<------ End Send Email to User ---------->>>

			 \Session::flash('success_message', __('admin.success_update'));
		   return redirect('panel/admin/verification/members');

		} catch (\Exception $e) {}
	  }

	}// End Method

	public function billingStore(Request $request)
	{
		$this->settings->company = $request->company;
		$this->settings->country = $request->country;
		$this->settings->address = $request->address;
		$this->settings->city = $request->city;
		$this->settings->zip = $request->zip;
		$this->settings->vat = $request->vat;
		$this->settings->save();

		\Session::flash('success_message', __('admin.success_update'));
		return back();

	}

	public function emailSettings(Request $request)
	{
		$request->validate([
				'MAIL_FROM_ADDRESS' => 'required'
			]);

		$request->MAIL_ENCRYPTION = strtolower($request->MAIL_ENCRYPTION);

		$this->settings->email_no_reply = $request->MAIL_FROM_ADDRESS;
		$this->settings->save();

		foreach ($request->except(['_token']) as $key => $value) {
			Helper::envUpdate($key, $value);

			if ($value == $request->MAIL_FROM_ADDRESS) {
				Helper::envUpdate('MAIL_FROM_ADDRESS', ' "'.$request->MAIL_FROM_ADDRESS.'" ', true);
			}
		}

		\Session::flash('success_message', __('admin.success_update'));

		return back();

	}

	public function updateSocialLogin(Request $request)
	{
		$this->settings->facebook_login = $request->facebook_login;
		$this->settings->google_login = $request->google_login;
		$this->settings->twitter_login = $request->twitter_login;
		$this->settings->save();

		foreach ($request->except(['_token']) as $key => $value) {
			Helper::envUpdate($key, $value);
		}

		\Session::flash('success_message', __('admin.success_update'));
		return back();
	}

	public function storage(Request $request)
	{
		$messages = [
			'APP_URL.required' => __('validation.required', ['attribute' => 'App URL']),
			'APP_URL.url' => __('validation.url', ['attribute' => 'App URL'])
		];

		$request->validate([
				'APP_URL'      => 'required|url',
				'AWS_ACCESS_KEY_ID' => 'required_if:FILESYSTEM_DRIVER,==,s3',
				'AWS_SECRET_ACCESS_KEY' => 'required_if:FILESYSTEM_DRIVER,==,s3',
				'AWS_DEFAULT_REGION' => 'required_if:FILESYSTEM_DRIVER,==,s3',
				'AWS_BUCKET' => 'required_if:FILESYSTEM_DRIVER,==,s3',

				'DOS_ACCESS_KEY_ID' => 'required_if:FILESYSTEM_DRIVER,==,dospace',
				'DOS_SECRET_ACCESS_KEY' => 'required_if:FILESYSTEM_DRIVER,==,dospace',
				'DOS_DEFAULT_REGION' => 'required_if:FILESYSTEM_DRIVER,==,dospace',
				'DOS_BUCKET' => 'required_if:FILESYSTEM_DRIVER,==,dospace',

				'WAS_ACCESS_KEY_ID' => 'required_if:FILESYSTEM_DRIVER,==,wasabi',
				'WAS_SECRET_ACCESS_KEY' => 'required_if:FILESYSTEM_DRIVER,==,wasabi',
				'WAS_DEFAULT_REGION' => 'required_if:FILESYSTEM_DRIVER,==,wasabi',
				'WAS_BUCKET' => 'required_if:FILESYSTEM_DRIVER,==,wasabi',

				'BACKBLAZE_ACCOUNT_ID' => 'required_if:FILESYSTEM_DRIVER,==,backblaze',
				'BACKBLAZE_APP_KEY' => 'required_if:FILESYSTEM_DRIVER,==,backblaze',
				'BACKBLAZE_BUCKET' => 'required_if:FILESYSTEM_DRIVER,==,backblaze',
				'BACKBLAZE_BUCKET_ID' => 'required_if:FILESYSTEM_DRIVER,==,backblaze',
				'BACKBLAZE_BUCKET_REGION' => 'required_if:FILESYSTEM_DRIVER,==,backblaze',

				'VULTR_ACCESS_KEY' => 'required_if:FILESYSTEM_DRIVER,==,vultr',
				'VULTR_SECRET_KEY' => 'required_if:FILESYSTEM_DRIVER,==,vultr',
				'VULTR_REGION' => 'required_if:FILESYSTEM_DRIVER,==,vultr',
				'VULTR_BUCKET' => 'required_if:FILESYSTEM_DRIVER,==,vultr',
			], $messages);

			// Enabled/Disabled DigitalOcean CDN
			if (! $request->DOS_CDN) {
				Helper::envUpdate('DOS_CDN', null);
			}

		foreach ($request->except(['_token']) as $key => $value) {
			if ($value == $request->APP_URL) {
				$value = trim($value, '/');
			}

			Helper::envUpdate($key, $value);
		}

		return back()->withSuccessMessage(__('admin.success_update'));

	} // End Method

	public function uploadImageEditor(Request $request)
	{
		if ($request->hasFile('upload')) {

			$path = config('path.admin');

			$validator = Validator::make($request->all(), [
				'upload' => 'required|mimes:jpg,gif,png,jpe,jpeg|max:'.$this->settings->file_size_allowed.'',
						]);

			if ($validator->fails()) {
 	        return response()->json([
 			        'uploaded' => 0,
							'error' => ['message' => __('general.upload_image_error_editor').' '.Helper::formatBytes($this->settings->file_size_allowed * 1024)],
 			    ]);
 	    } //<-- Validator


        $originName = $request->file('upload')->getClientOriginalName();
        $fileName = pathinfo($originName, PATHINFO_FILENAME);
        $extension = $request->file('upload')->getClientOriginalExtension();
        $fileName = str_random().'_'.time().'.'.$extension;

				$request->file('upload')->storePubliclyAs($path, $fileName);

        $CKEditorFuncNum = $request->input('CKEditorFuncNum');
        $url = Helper::getFile($path.$fileName);
        $msg = 'Image uploaded successfully';
        $response = "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg');</script>";

				return response()->json([ 'fileName' => $fileName, 'uploaded' => true, 'url' => $url, ]);
    }
	}// End Method

	public function blog()
	{
		$data = Blogs::orderBy('id','desc')->paginate(50);
		return view('admin.blog', ['data' => $data]);
	}//<--- End Method

	public function createBlogStore(Request $request)
	{
		$path = config('path.admin');

		$rules = [
            'title'     => 'required',
						'thumbnail' => 'required|dimensions:min_width=650,min_height=430',
						'tags'      => 'required',
						'content'   => 'required',
	     ];

		$this->validate($request, $rules);

		// Image
		if( $request->hasFile('thumbnail') ) {

			$image     =  $request->file('thumbnail');
			$extension = $image->getClientOriginalExtension();
			$thumbnail = str_random(55).'.'.$extension;

		$imageResize  = Image::make($image)->orientate()->resize(650, null, function ($constraint) {
			$constraint->aspectRatio();
			$constraint->upsize();
		})->encode($extension);

		  Storage::put($path.$thumbnail, $imageResize, 'public');

		} // HasFile Image

		$data = New Blogs;
		$data->slug = str_slug($request->title);
		$data->title = $request->title;
		$data->image = $thumbnail;
		$data->tags = $request->tags;
		$data->content = $request->content;
		$data->user_id = auth()->user()->id;
		$data->save();

		\Session::flash('success_message',__('admin.success_add'));
		return redirect('panel/admin/blog');

	}//<--- END METHOD

	public function editBlog($id)
	{
		$data = Blogs::findOrFail($id);

		return view('admin.edit-blog', ['data' => $data ]);

	}//<--- End Method

	public function updateBlog(Request $request)
	{
		$data = Blogs::findOrFail($request->id);

		$path = config('path.admin');

		$rules = [
            'title'   => 'required',
						'thumbnail' => 'dimensions:min_width=650,min_height=430',
						'tags'    => 'required',
						'content' => 'required',
	     ];

		$this->validate($request, $rules);

		$thumbnail = $data->image;

		// Image
		if( $request->hasFile('thumbnail') ) {

			$image     =  $request->file('thumbnail');
			$extension = $image->getClientOriginalExtension();
			$thumbnail = str_random(55).'.'.$extension;

		$imageResize  = Image::make($image)->orientate()->resize(650, null, function ($constraint) {
			$constraint->aspectRatio();
			$constraint->upsize();
		})->encode($extension);

			Storage::put($path.$thumbnail, $imageResize, 'public');

		// Delete Old Thumbnail
		Storage::delete($path.$data->image);

		} // HasFile Image

		$data->title = $request->title;
		$data->slug = str_slug($request->title);
		$data->image = $thumbnail;
		$data->tags = $request->tags;
		$data->content = $request->content;
		$data->save();

		return back()->withSuccessMessage(__('admin.success_update'));

	}//<--- END METHOD

	public function deleteBlog($id)
	{
		$data = Blogs::findOrFail($id);

		$path = config('path.admin');

		// Delete Old Thumbnail
		Storage::delete($path.$data->image);

		$data->delete();

		return redirect('panel/admin/blog')->withSuccessMessage(__('admin.blog_deleted'));

	}//<--- END METHOD

	public function resendConfirmationEmail($id)
	{
		$user =  User::whereId($id)->whereStatus('pending')->firstOrFail();

		$confirmation_code = Str::random(100);

		//send verification mail to user
	 $_username      = $user->username;
	 $_email_user    = $user->email;
	 $_title_site    = $this->settings->title;
	 $_email_noreply = $this->settings->email_no_reply;

	 app()->setLocale($user->language);

	 Mail::send('emails.verify', array('confirmation_code' => $confirmation_code, 'isProfile' => null),
	 function($message) use (
			 $_username,
			 $_email_user,
			 $_title_site,
			 $_email_noreply
	 ) {
							$message->from($_email_noreply, $_title_site);
							$message->subject(__('users.title_email_verify'));
							$message->to($_email_user,$_username);
					});

					$user->update(['confirmation_code' => $confirmation_code]);

		\Session::flash('success_message', __('general.send_success'));

    return redirect('panel/admin/members');

	}

	public function deposits()
	{
		$data = Deposits::orderBy('id', 'desc')->paginate(30);
		return view('admin.deposits')->withData($data);
	}//<--- End Method

	public function depositsView($id)
	{
		$data = Deposits::findOrFail($id);
		return view('admin.deposits-view')->withData($data);
	}//<--- End Method

	public function approveDeposits(Request $request)
	{
		$sql = Deposits::findOrFail($request->id);

		//<------ Send Email to User ---------->>>
		$sender       = $this->settings->email_no_reply;
		$titleSite    = $this->settings->title;
		$fullNameUser = $sql->user()->name;
		$emailUser    = $sql->user()->email;
		$language     = $sql->user()->language;

		Mail::send('emails.transfer_verification', [
			'body' => __('general.info_transfer_verified', ['amount' => Helper::amountFormat($sql->amount)]),
			'type' => 'approve',
			'title_site' => $titleSite,
			'fullname'   => $fullNameUser
		],
			function($message) use ($sender, $fullNameUser, $titleSite, $emailUser)
				{
						$message->from($sender, $titleSite)
										->to($emailUser, $fullNameUser)
										->subject(__('general.transfer_verified').' - '.$titleSite);
				});


			//<------ End Send Email to User ---------->>>

			$sql->status = 'active';
			$sql->save();

			// Add Funds to User
			User::find($sql->user()->id)->increment('wallet', $sql->amount);

		return redirect('panel/admin/deposits');
	}//<--- END METHOD

	public function deleteDeposits(Request $request)
	{
		$path = config('path.admin');
	  $sql = Deposits::findOrFail($request->id);

		if (isset($sql->user()->name)) {
			//<------ Send Email to User ---------->>>
		 $sender       = $this->settings->email_no_reply;
		 $titleSite    = $this->settings->title;
		 $fullNameUser = $sql->user()->name;
		 $emailUser   =  $sql->user()->email;

		 Mail::send('emails.transfer_verification', array(
			 'body' => __('general.info_transfer_not_verified', ['amount' => Helper::amountFormat($sql->amount)]),
			 'type' => 'not_approve',
			 'title_site' => $titleSite,
			 'fullname'   => $fullNameUser
		 ),
			 function($message) use ($sender, $fullNameUser, $titleSite, $emailUser)
				 {
						 $message->from($sender, $titleSite)
										 ->to($emailUser, $fullNameUser)
										 ->subject(__('general.transfer_not_verified').' - '.$titleSite);
				 });
			 //<------ End Send Email to User ---------->>>
		}

			// Delete Image
			Storage::delete($path.$sql->screenshot_transfer);

	      $sql->delete();

      return redirect('panel/admin/deposits');

	}//<--- End Method

	public function loginAsUser(Request $request)
	{
		auth()->logout();
		auth()->loginUsingId($request->id);
		return redirect('settings/page');
	}

	public function customCssJs(Request $request)
	{
		$sql = $this->settings;
		$sql->custom_css = $request->custom_css;
		$sql->custom_js = $request->custom_js;
		$sql->save();

		return back()->withSuccessMessage(__('admin.success_update'));

	}

	public function pwa(Request $request)
	{
		$allImgs = $request->file('files');

		if ($allImgs) {
			foreach ($allImgs as $key => $file) {
			$filename = md5(uniqid()).'.'.$file->getClientOriginalExtension();
			$file->move(public_path('images/icons'), $filename);

			\File::delete(env($key));

			$envIcon = '/images/icons/' . $filename;
			Helper::envUpdate($key, $envIcon);
			}
		}

		// Updaye Short Name
		Helper::envUpdate('PWA_SHORT_NAME', ' "'.$request->PWA_SHORT_NAME.'" ', true);

		$sql = $this->settings;
		$sql->status_pwa = $request->status_pwa;
		$sql->save();

		\Artisan::call('cache:clear');
		\Artisan::call('view:clear');

		return back()->withSuccessMessage(__('admin.success_update'));

	}

	public function getFileVerification($filename)
  {
		$filename = config('path.verification').$filename;

  	return Storage::download($filename, null, [], null);
  }

	public function storeAnnouncements(Request $request)
	{
		$this->settings->announcement = $request->announcement_content;
		$this->settings->announcement_show = $request->announcement_show;
		$this->settings->type_announcement = $request->type_announcement;
		$this->settings->announcement_cookie = Str::random(20);
		$this->settings->save();

		return back()->withSuccessMessage(__('admin.success_update'));
	}

	public function approvePost(Request $request)
	{
		$post = Updates::findOrFail($request->id);
		$post->date = now();
		$post->status = 'active';
		$post->save();

		// Notify to user - destination, author, type, target
		Notifications::send($post->user_id, 1, 8, $post->id);

		// Event to listen
		event(new NewPostEvent($post));

		return back()->withSuccessMessage(__('general.approve_post_success'));
	}

	public function roleAndPermissions($id, Request $request)
	{
		$user = User::findOrFail($id);

		if ($user->id == 1 || $user->id == auth()->user()->id) {
			\Session::flash('info_message', __('admin.user_no_edit'));
			return redirect('panel/admin/members');
		}

		$permissions = explode(',', $user->permissions);

    	return view('admin.role-and-permissions-member')->with([
				'user' => $user,
				'permissions' => $permissions,
			]);

	}//<--- End Method

	public function storeRoleAndPermissions(Request $request)
	{
		if (isset($request->limited_access) && isset($request->permissions)) {
			return back()->withErrorMessage(__('general.give_access_error'));
		}

		if (!isset($request->limited_access) && isset($request->permissions)) {
			foreach ($request->permissions as $key) {

				if (isset($request->permissions)) {
					 $permissions[] = $key;
				}
			}

			$permissions = implode( ',', $permissions);
		} else {
			$permissions = 'limited_access';
		}

		$permission = $request->permission ?: 'none';

    $user = User::findOrFail($request->id);
	  $user->role = $request->role;
		$user->permission = $request->role == 'admin' ? $permission : 'none';
		$user->permissions = $request->role == 'admin' ? $permissions : null;
    $user->save();

    return back()->withSuccessMessage(__('admin.success_update'));

	}//<--- End Method

	public function saveLiveStreaming(Request $request)
	{
		$this->settings->live_streaming_status        = $request->live_streaming_status;
		$this->settings->agora_app_id                 = $request->agora_app_id;
		$this->settings->live_streaming_minimum_price = $request->live_streaming_minimum_price;
		$this->settings->live_streaming_max_price     = $request->live_streaming_max_price;
		$this->settings->live_streaming_free          = $request->live_streaming_free;
		$this->settings->limit_live_streaming_paid    = $request->limit_live_streaming_paid;
		$this->settings->limit_live_streaming_free    = $request->limit_live_streaming_free;
    $this->settings->save();

		return back()->withSuccessMessage(__('admin.success_update'));
	}//<--- End Method

	public function referrals()
	{
		$data = Referrals::orderBy('id', 'desc')->paginate(20);

		return view('admin.referrals')->withData($data);
	}

	public function shopStore(Request $request)
	{
		if (! $request->custom_content && ! $request->physical_products && ! $request->digital_product_sale) {
			return back()->withErrors([
				'errors' => __('general.error_type_sale')
			]);
		}

		$rules = [
			'min_price_product' => 'required|numeric|min:1',
			'max_price_product' => 'required|numeric|min:1',
        ];

		$this->validate($request, $rules);

		$this->settings->shop        = $request->shop;
		$this->settings->min_price_product = $request->min_price_product;
		$this->settings->max_price_product = $request->max_price_product;
		$this->settings->digital_product_sale = $request->digital_product_sale;
		$this->settings->custom_content = $request->custom_content;
		$this->settings->physical_products = $request->physical_products;
    $this->settings->save();

		return back()->withSuccessMessage(__('admin.success_update'));
	}//<--- End Method

	public function products()
	{
		$data = Products::orderBy('id', 'desc')->paginate(20);

		return view('admin.products')->withData($data);
	}

	public function productDelete($id)
	{
		$item = Products::findOrFail($id);

		$path = config('path.shop');

    // Delete Notifications
    Notifications::whereType(15)->whereTarget($item->id)->delete();

    // Delete Preview
    foreach ($item->previews as $previews) {
      Storage::delete($path.$previews->name);
    }

    // Delete file
    Storage::delete($path.$item->file);

    // Delete purchases
    $item->purchases()->delete();

    // Delete item
    $item->delete();

		return back();
	}

	public function sales()
	{
		$sales = Purchases::orderBy('id', 'desc')->paginate(10);

		return view('admin.sales')->withSales($sales);
	}

	public function salesRefund($id)
  {
    $purchase = Purchases::findOrFail($id);

        if ($purchase) {

          $amount = $purchase->transactions()->amount;

          $taxes = TaxRates::whereIn('id', collect(explode('_', $purchase->transactions()->taxes)))->get();
          $totalTaxes = ($amount * $taxes->sum('percentage') / 100);

          // Total paid by buyer
          $amountRefund = number_format($amount + $purchase->transactions()->transaction_fee + $totalTaxes, 2, '.', '');

          // Get amount referral (if exist)
          $referralTransaction = ReferralTransactions::whereTransactionsId($purchase->transactions()->id)->first();

          if ($purchase->transactions()->referred_commission && $referralTransaction) {
            User::find($referralTransaction->referred_by)->decrement('balance', $referralTransaction->earnings);

            // Delete $referralTransaction
            $referralTransaction->delete();
          }

          // Add funds to wallet buyer
          $purchase->user()->increment('wallet', $amountRefund);

          // User Balnce Current
					$userBalance = $purchase->products()->user()->balance;

					// If the creator has withdrawn their entire balance remove from withdrawal
					$withdrawalPending = Withdrawals::whereUserId($purchase->products()->user()->id)->whereStatus('pending')->first();

					// Remove creator funds
          if ($userBalance <> 0.00) {
            $purchase->products()->user()->decrement('balance', $purchase->transactions()->earning_net_user);
          } elseif ($withdrawalPending) {
              $withdrawalPending->decrement('amount', $amountRefund);
          } elseif ($userBalance == 0.00 && ! $withdrawalPending) {
          	$purchase->products()->user()->decrement('balance', $purchase->transactions()->earning_net_user);
          }

          // Delete transaction
          $purchase->transactions()->delete();

          // Delete purchase
          $purchase->delete();

        }

        return back()->withSuccessMessage(__('general.refund_success'));
  }// end salesRefund

	// START
	public function shopCategories()
	{
		$categories      = ShopCategories::orderBy('id', 'desc')->get();
		$totalCategories = count($categories);

		return view('admin.shop-categories', compact('categories', 'totalCategories'));
	}//<--- END METHOD

	public function addShopCategories()
	{
		return view('admin.add-shop-categories');
	}//<--- END METHOD

	public function storeShopCategories(Request $request)
	{
		Validator::extend('ascii_only', function($attribute, $value, $parameters){
    		return !preg_match('/[^x00-x7F\-]/i', $value);
		});

		$rules = [
          'name'        => 'required',
	        'slug'        => 'required|ascii_only',
        ];

		$this->validate($request, $rules);

		$sql              = new ShopCategories();
		$sql->name        = $request->name;
		$sql->slug        = $request->slug;
		$sql->save();

		\Session::flash('success_message', __('admin.success_add_category'));

    	return redirect('panel/admin/shop-categories');

	}//<--- END METHOD

	public function editShopCategories($id)
	{
		$categories = ShopCategories::find($id);

		return view('admin.edit-shop-categories')->with('categories', $categories);

	}//<--- END METHOD

	public function updateShopCategories(Request $request)
	{
		$categories = ShopCategories::findOrFail($request->id);

		Validator::extend('ascii_only', function($attribute, $value, $parameters){
    		return !preg_match('/[^x00-x7F\-]/i', $value);
		});

		$rules = [
          'name'        => 'required',
	        'slug'        => 'required|ascii_only',
	     ];

			 $this->validate($request, $rules);

		// UPDATE CATEGORY
		$categories->name   = $request->name;
		$categories->slug   = $request->slug;
		$categories->save();

		\Session::flash('success_message', __('general.success_update'));
		return redirect('panel/admin/shop-categories');

	}//<--- END METHOD

	public function deleteShopCategories($id)
	{
			$categories   = ShopCategories::findOrFail($id);

			$userCategory = Products::where('category', $id)->update(['category' => null]);

			// Delete Category
			$categories->delete();

			return redirect('panel/admin/shop-categories');
	}//<--- END METHOD

	public function testSMTP()
	{
		try {
				Mail::raw('Hi, Testing SMTP...', function ($mail) {
		    $mail->from($this->settings->email_no_reply)
		        ->to($this->settings->email_admin)
						->subject('Testing SMTP...');

			});
		} catch (\Exception $e) {
			return back()->withErrors([
				'errors' => $e->getMessage(),
			]);
		}

		return back()->withSuccessMessage(__('general.send_success').' -- '.$this->settings->email_admin);

	}//<--- END METHOD

	public function savePushNotifications(Request $request)
	{
		$this->settings->push_notification_status  = $request->push_notification_status;
		$this->settings->onesignal_appid           = $request->onesignal_appid;
		$this->settings->onesignal_restapi         = $request->onesignal_restapi;
		$this->settings->save();

		return back()->withSuccessMessage(__('admin.success_update'));
	}//<--- End Method

	  /**
	 * Get data Earnings Dashboard Admin
	 *
	 * @return Response
	 */
	public function getDataChart(Request $request)
	{
	  if (! $request->expectsJson()) {
		abort(401);
	  }

	  switch ($request->range) {
		case 'month':
		  $month = date('m');
		  $year  = date('Y');
		  $daysMonth = Helper::daysInMonth($month, $year);
		  $dateFormat = "$year-$month-";

		  $monthFormat  = __("months.$month");
		  $currencySymbol = $this->settings->currency_symbol;

		  for ($i=1; $i <= $daysMonth; ++$i) {
			$date = date('Y-m-d', strtotime($dateFormat.$i));
			$payments = Transactions::whereDate('created_at', '=', $date)->sum('earning_net_admin');

			$monthsData[] =  "$monthFormat $i";
			$earningNetUser = $payments;
			$earningNetUserSum[] = $earningNetUser;
		  }

		  $label = $monthsData;
		  $data = $earningNetUserSum;

		  break;

		  case 'last-month':
			$month = date('m', strtotime('-1 month'));
			$year  = date('Y');
			$daysMonth = Helper::daysInMonth($month, $year);
			$dateFormat = "$year-$month-";

			$monthFormat  = __("months.$month");
			$currencySymbol = $this->settings->currency_symbol;

			for ($i=1; $i <= $daysMonth; ++$i) {
			  $date = date('Y-m-d', strtotime($dateFormat.$i));
			  $payments = Transactions::whereDate('created_at', '=', $date)->sum('earning_net_admin');

			  $monthsData[] =  "$monthFormat $i";
			  $earningNetUser = $payments;
			  $earningNetUserSum[] = $earningNetUser;
			}

			$label = $monthsData;
			$data = $earningNetUserSum;

			break;

			case 'year':
			  $year  = date('Y');
			  $dateFormat = "$year-";
			  $currencySymbol = $this->settings->currency_symbol;

			  for ($i=1; $i <= 12; ++$i) {
				$month = str_pad($i, 2, "0", STR_PAD_LEFT);
				$date = date('Y-m', strtotime($dateFormat.$month));
				$payments = Transactions::where('created_at', 'LIKE', '%'.$date.'%')->sum('earning_net_admin');

				$monthsData[] =  __("months.$month");
				$earningNetUser = $payments;
				$earningNetUserSum[] = $earningNetUser;
			  }

			  $label = $monthsData;
			  $data = $earningNetUserSum;
			  break;

		default:

		return response()->json([
		  'success' => false
		], 401);

		  break;
	  }

	  return response()->json([
		'success' => true,
		'labels'  => $label,
		'datasets' => $data
		]);
	}

	public function clearCache()
	{
		 // Clear Cache, Config and Views
		 \Artisan::call('cache:clear');
		 \Artisan::call('config:clear');
		 \Artisan::call('view:clear');

		 $pathLogFile = storage_path("logs".DIRECTORY_SEPARATOR."laravel.log");

		 try {
			collect(Storage::disk('default')->listContents('.cache', true))
				->each(function($file) {
				Storage::disk('default')->deleteDirectory($file['path']);
				Storage::disk('default')->delete($file['path']);
			});

			// Delete Log file
			if (auth()->user()->isSuperAdmin()) {
				if (file_exists($pathLogFile)) {
					unlink($pathLogFile);
				}
			}

		  } catch (\Exception $e) {}

		  return redirect('panel/admin/maintenance/mode')
			->withSuccessMessage(trans('general.successfully_cleaned'));

	}// End method

	public function saveStoriesSettings(Request $request)
	{
		$this->settings->story_status = $request->story_status;
		$this->settings->story_image = $request->story_image;
		$this->settings->story_text = $request->story_text;
		$this->settings->story_max_videos_length = $request->story_max_videos_length;
		$this->settings->save();

		return back()->withSuccessMessage(__('admin.success_update'));
	}//<--- End Method

	public function storiesPosts()
	{
		$data = Stories::latest()->paginate(20);

		return view('admin.stories-posts', ['data' => $data]);
	}

	public function deleteStory($id)
	{
		$pathStories = config('path.stories');
        $story = Stories::findOrFail($id);
        $media = $story->media;

		//Delete Views
		$media[0]->views()->delete();
		//Delete Media
		Storage::delete($pathStories.$media[0]->name);
		$media[0]->delete();
		//Delete Story
		$story->delete();

		return back()->withSuccessMessage(__('general.success_removed'));
	}

	public function storiesBackgrounds()
	{
		$data = StoryBackgrounds::orderBy('id', 'desc')->paginate(20);
		return view('admin.stories-backgrounds', ['data' => $data]);
	}

	public function addStoryBackground(Request $request)
	{
		$temp  = '/temp/';
	  	$path  = 'img/stories-bg/';

		  $request->validate([
			'image' => 'required|mimes:jpg,png,jpe,jpeg'
		  ]);

		if ($request->hasFile('image')) {
			$extension = $request->file('image')->getClientOriginalExtension();
			$file = str_random(5).time().'.'.$extension;

			if ($request->file('image')->move($temp, $file)) {
				\File::copy($temp.$file, $path.$file);
				\File::delete($temp.$file);
				}// End File

				$sql = new StoryBackgrounds();
				$sql->name = $file;
				$sql->save();
			} // HasFile

		return back();
	}

	public function deleteStoryBackground($id)
	{
		$path  = 'img/stories-bg/';
		$storyBackground = StoryBackgrounds::findOrFail($id);

		\File::delete($path.$storyBackground->name);
		$storyBackground->delete();

		return back()->withSuccessMessage(__('general.success_removed'));
	}

	public function storiesFonts()
	{
		$data = StoryFonts::orderBy('id', 'desc')->paginate(20);
		return view('admin.stories-fonts', ['data' => $data]);
	}

	public function addStoryFont(Request $request)
	{
		$request->validate([
			'name' => 'required',
		]);

		$sql = new StoryFonts();
		$sql->name = $request->name;
		$sql->save();

		return back();
	}

	public function deleteStoryFont($id)
	{
		$font = StoryFonts::findOrFail($id)->delete();
		return back()->withSuccessMessage(__('general.success_removed'));
	}

}// End Class
