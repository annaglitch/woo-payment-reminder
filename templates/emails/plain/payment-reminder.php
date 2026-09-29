<?php
/**
 * Payment reminder – plain text version.
 * Used only when the email type is set to "Plain text".
 * The QR code and invoice link are not included in plain text.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$bank_transfer_methods = array( 'bacs' );

$is_bank_transfer = in_array( $order->get_payment_method(), $bank_transfer_methods, true );
$show_pay_link    = ! $is_bank_transfer && $order->needs_payment();

if ( $order->get_billing_first_name() ) {
    printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) );
    echo "\n\n";
}

echo wp_strip_all_tags( sprintf( __( 'We noticed that your order <strong>#%s</strong> is still awaiting payment.', 'woo-payment-reminder' ), $order->get_order_number() ) ) . "\n\n";

if ( ( $is_bank_transfer && ! $order->is_paid() ) || $show_pay_link ) {
    echo __( 'To complete your purchase, please proceed with the payment at your earliest convenience.', 'woo-payment-reminder' ) . "\n\n";
}

if ( $show_pay_link ) {
    echo __( 'Payment link:', 'woo-payment-reminder' ) . "\n";
    echo esc_url( $order->get_checkout_payment_url() ) . "\n\n";
}

echo __( 'If you have already completed the payment, please ignore this message.', 'woo-payment-reminder' ) . "\n\n";
echo __( 'Thank you for shopping with us.', 'woo-payment-reminder' ) . "\n";
