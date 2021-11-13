<?php
use gburtini\Distributions\Accessories\BetaFunction;

class BetaFunctionTest extends PHPUnit_Framework_TestCase
{
    public function testBetaFunction()
    {
        $accuracy = 1e-8;
        $this->assertEquals(1/12, BetaFunction::betaFunction(2, 3), "Expected beta function to perform appropriately B(2,3)=1/12.", $accuracy);
        $this->assertEquals(4.477609374, BetaFunction::betaFunction(1.5, 0.2), "Expected beta function to perform appropriately B(1.5, 0.2)=4.477.", $accuracy);

        /* Test data generated with SciPy */
        $testCases = array(
            array( 'x' =>  0.430945683717 , 'y' =>  0.08359456305 , 'beta' =>  13.6736295896 ),
            array( 'x' =>  0.465627845477 , 'y' =>  0.473132509051 , 'beta' =>  3.42574261792 ),
            array( 'x' =>  0.467564911358 , 'y' =>  0.12936983064 , 'beta' =>  9.19947931988 ),
            array( 'x' =>  0.566167199156 , 'y' =>  0.181392630725 , 'beta' =>  6.51354453448 ),
            array( 'x' =>  0.499897565786 , 'y' =>  0.882357063044 , 'beta' =>  2.16176476346 ),
            array( 'x' =>  0.433844037797 , 'y' =>  0.723166430574 , 'beta' =>  2.77061424524 ),
            array( 'x' =>  0.360152757935 , 'y' =>  0.815790689709 , 'beta' =>  3.06497889729 ),
            array( 'x' =>  0.372948205175 , 'y' =>  0.103065744995 , 'beta' =>  11.8111470136 ),
            array( 'x' =>  0.51794973021 , 'y' =>  0.269034680081 , 'beta' =>  4.87252568192 ),
            array( 'x' =>  0.0529933652743 , 'y' =>  0.167090672931 , 'beta' =>  24.5444009002 ),
            array( 'x' =>  0.252081613995 , 'y' =>  0.754652006347 , 'beta' =>  4.3989513401 ),
            array( 'x' =>  0.328692498807 , 'y' =>  0.784618619615 , 'beta' =>  3.39610762199 ),
            array( 'x' =>  0.239412700202 , 'y' =>  0.958006125694 , 'beta' =>  4.23809706562 ),
            array( 'x' =>  0.366468381904 , 'y' =>  0.333313225204 , 'beta' =>  5.00892370745 ),
            array( 'x' =>  0.702797799161 , 'y' =>  0.836201479469 , 'beta' =>  1.64003735053 ),
            array( 'x' =>  0.00967972437507 , 'y' =>  0.0134983226213 , 'beta' =>  177.3545174 ),
            array( 'x' =>  0.253874064073 , 'y' =>  0.979309526736 , 'beta' =>  3.96834049841 ),
            array( 'x' =>  0.347484523957 , 'y' =>  0.705688370545 , 'beta' =>  3.40240380327 ),
            array( 'x' =>  0.803359803704 , 'y' =>  0.46641766796 , 'beta' =>  2.44138804009 ),
            array( 'x' =>  0.317711302312 , 'y' =>  0.203731651771 , 'beta' =>  7.45403948314 ),
            array( 'x' =>  0.158158150425 , 'y' =>  0.8513794904 , 'beta' =>  6.57224605318 ),
            array( 'x' =>  0.779592476895 , 'y' =>  0.0859003871202 , 'beta' =>  12.0547311238 ),
            array( 'x' =>  0.0464174301699 , 'y' =>  0.473165703074 , 'beta' =>  23.0362492697 ),
            array( 'x' =>  0.952196113894 , 'y' =>  0.867841765909 , 'beta' =>  1.20491562546 ),
            array( 'x' =>  0.225301481866 , 'y' =>  0.13947665623 , 'beta' =>  11.1420612817 ),
        );

        foreach ($testCases as $test) {
            $x = $test['x'];
            $y = $test['y'];
            $beta = $test['beta'];
            $this->assertEquals($beta, BetaFunction::betaFunction($x, $y), 'Expected beta function value B($x, $y) not matching', $accuracy);
        }
    }


    // NOTE: regularized
    public function testIncompleteBetaFunction()
    {
        $this->assertEquals(0.784, BetaFunction::incompleteBetaFunction(0.4, 1, 3), "Check behavior of regularized incomplete beta.", 0.0001);

        $accuracy = 1e-8;

        /* Test data generated with SciPy */
        $testCases = array(
            array( 'a' =>  0.166322543519 , 'b' =>  0.194040909865 , 'x' =>  0.474548681807 , 'incbeta' =>  0.532411845595 ),
            array( 'a' =>  0.367960863626 , 'b' =>  0.0201047846764 , 'x' =>  0.784778979943 , 'incbeta' =>  0.0690485112932 ),
            array( 'a' =>  0.194254889084 , 'b' =>  0.684937388736 , 'x' =>  0.00181194818231 , 'incbeta' =>  0.261930068093 ),
            array( 'a' =>  0.67505276424 , 'b' =>  0.139617850166 , 'x' =>  0.729375662773 , 'incbeta' =>  0.228234672022 ),
            array( 'a' =>  0.445940667647 , 'b' =>  0.400969517729 , 'x' =>  0.665432165334 , 'incbeta' =>  0.566130082417 ),
            array( 'a' =>  0.429504421412 , 'b' =>  0.454647921311 , 'x' =>  0.148231420907 , 'incbeta' =>  0.2832667991 ),
            array( 'a' =>  0.587132584349 , 'b' =>  0.744637286471 , 'x' =>  0.721709317413 , 'incbeta' =>  0.73899334019 ),
            array( 'a' =>  0.0614601976932 , 'b' =>  0.853260999397 , 'x' =>  0.432354150086 , 'incbeta' =>  0.938795256056 ),
            array( 'a' =>  0.939701210187 , 'b' =>  0.522871803953 , 'x' =>  0.961204031467 , 'incbeta' =>  0.82410982661 ),
            array( 'a' =>  0.543494287762 , 'b' =>  0.345114147569 , 'x' =>  0.789733440588 , 'incbeta' =>  0.556505839324 ),
            array( 'a' =>  0.984760425767 , 'b' =>  0.643588345811 , 'x' =>  0.110218732718 , 'incbeta' =>  0.0751646909898 ),
            array( 'a' =>  0.00799640178786 , 'b' =>  0.383358655653 , 'x' =>  0.467372773811 , 'incbeta' =>  0.980329292546 ),
            array( 'a' =>  0.253002522746 , 'b' =>  0.759465511431 , 'x' =>  0.339129696784 , 'incbeta' =>  0.700351918194 ),
            array( 'a' =>  0.492942281953 , 'b' =>  0.152964197938 , 'x' =>  0.72588665349 , 'incbeta' =>  0.303800487878 ),
            array( 'a' =>  0.214467351794 , 'b' =>  0.21105358146 , 'x' =>  0.415429468524 , 'incbeta' =>  0.46722576948 ),
            array( 'a' =>  0.988280897251 , 'b' =>  0.224137738825 , 'x' =>  0.585672291383 , 'incbeta' =>  0.181457050111 ),
            array( 'a' =>  0.858103096535 , 'b' =>  0.894252534682 , 'x' =>  0.0722787118099 , 'incbeta' =>  0.0951712688139 ),
            array( 'a' =>  0.374679209521 , 'b' =>  0.689048878227 , 'x' =>  0.429182787988 , 'incbeta' =>  0.628842551959 ),
            array( 'a' =>  0.257003814918 , 'b' =>  0.256380795441 , 'x' =>  0.0546486833402 , 'incbeta' =>  0.258307894311 ),
            array( 'a' =>  0.706641266659 , 'b' =>  0.911978246825 , 'x' =>  0.150496303379 , 'incbeta' =>  0.245211070328 ),
            array( 'a' =>  0.891513327337 , 'b' =>  0.832720084333 , 'x' =>  0.635737710959 , 'incbeta' =>  0.602393684994 ),
            array( 'a' =>  0.368422024194 , 'b' =>  0.178504212522 , 'x' =>  0.0929279442354 , 'incbeta' =>  0.150215552236 ),
            array( 'a' =>  0.257055168434 , 'b' =>  0.732480138902 , 'x' =>  0.923775172441 , 'incbeta' =>  0.951423867329 ),
            array( 'a' =>  0.612777516276 , 'b' =>  0.449185997743 , 'x' =>  0.533448450921 , 'incbeta' =>  0.431138553137 ),
            array( 'a' =>  0.457024695686 , 'b' =>  0.463493375777 , 'x' =>  0.271715226749 , 'incbeta' =>  0.360946435756 ),
        );

        foreach ($testCases as $test) {
            $a = $test['a'];
            $b = $test['b'];
            $x = $test['x'];
            $incbeta = $test['incbeta'];
            $this->assertEquals($incbeta, BetaFunction::incompleteBetaFunction($x, $a, $b), 'Expected inomplete beta function value I($x, $a, $b) not matching', $accuracy);
        }
    }


    // NOTE: this function is regularized (B_x / B)
    public function testInverseIncompleteBetaFunction()
    {
        $this->assertEquals(0.1120959, BetaFunction::inverseIncompleteBetaFunction(0.3, 1, 3), "Check behavior of regularized inverse incomplete beta function (I_x(1, 3) at 0.3 should be ~0.112 according to Casio calculator)", 0.0001);
        $accuracy = 1e-8;

        /* Test data generated with SciPy */
        $testCases = array(
                array( 'a' =>  0.72683904095 , 'b' =>  0.598303296801 , 'x' =>  0.726220368055 , 'invbeta' =>  0.836839916719 ),
                array( 'a' =>  0.799792792385 , 'b' =>  0.234777890342 , 'x' =>  0.100686355216 , 'invbeta' =>  0.255666935818 ),
                array( 'a' =>  0.601999158797 , 'b' =>  0.704193867735 , 'x' =>  0.815604193876 , 'invbeta' =>  0.844029262353 ),
                array( 'a' =>  0.67127409906 , 'b' =>  0.369318641148 , 'x' =>  0.863083980384 , 'invbeta' =>  0.992084816128 ),
                array( 'a' =>  0.00729662905165 , 'b' =>  0.258346623188 , 'x' =>  0.608459573576 , 'invbeta' =>  8.53692775397e-29 ),
                array( 'a' =>  0.792999973436 , 'b' =>  0.954716096059 , 'x' =>  0.834024069363 , 'invbeta' =>  0.811096483869 ),
                array( 'a' =>  0.433835916773 , 'b' =>  0.420904964886 , 'x' =>  0.0305017074957 , 'invbeta' =>  0.00106586852517 ),
                array( 'a' =>  0.972154165551 , 'b' =>  0.311206531325 , 'x' =>  0.907998450528 , 'invbeta' =>  0.999513598713 ),
                array( 'a' =>  0.34720965161 , 'b' =>  0.793747912819 , 'x' =>  0.889938020098 , 'invbeta' =>  0.812599886796 ),
                array( 'a' =>  0.701318306882 , 'b' =>  0.77666488226 , 'x' =>  0.333338543844 , 'invbeta' =>  0.267496926084 ),
                array( 'a' =>  0.010697112587 , 'b' =>  0.100648498852 , 'x' =>  0.865156709778 , 'invbeta' =>  0.014063803744 ),
                array( 'a' =>  0.825596264227 , 'b' =>  0.739375533565 , 'x' =>  0.648378563075 , 'invbeta' =>  0.708608543808 ),
                array( 'a' =>  0.312776852667 , 'b' =>  0.15266121044 , 'x' =>  0.0676235309565 , 'invbeta' =>  0.00529734301525 ),
                array( 'a' =>  0.644290063387 , 'b' =>  0.72715541743 , 'x' =>  0.293320467947 , 'invbeta' =>  0.208369202071 ),
                array( 'a' =>  0.184222470451 , 'b' =>  0.171624657956 , 'x' =>  0.187425634327 , 'invbeta' =>  0.00470652366975 ),
                array( 'a' =>  0.940764366797 , 'b' =>  0.519659462985 , 'x' =>  0.236347412477 , 'invbeta' =>  0.378426385872 ),
                array( 'a' =>  0.417307401899 , 'b' =>  0.23077140833 , 'x' =>  0.0398878928764 , 'invbeta' =>  0.00405290840001 ),
                array( 'a' =>  0.226064525418 , 'b' =>  0.280735178088 , 'x' =>  0.250977697889 , 'invbeta' =>  0.0212001264416 ),
                array( 'a' =>  0.941710383249 , 'b' =>  0.851499124155 , 'x' =>  0.0547833583772 , 'invbeta' =>  0.0537202477547 ),
                array( 'a' =>  0.609013586207 , 'b' =>  0.751845415922 , 'x' =>  0.554193529914 , 'invbeta' =>  0.485681565591 ),
                array( 'a' =>  0.507691464047 , 'b' =>  0.44783095587 , 'x' =>  0.00182601884719 , 'invbeta' =>  1.15584503144e-05 ),
                array( 'a' =>  0.0797307903686 , 'b' =>  0.781813534164 , 'x' =>  0.0596389876755 , 'invbeta' =>  6.56990412913e-16 ),
                array( 'a' =>  0.918254216487 , 'b' =>  0.569486121079 , 'x' =>  0.149526039327 , 'invbeta' =>  0.214706078968 ),
                array( 'a' =>  0.669087501891 , 'b' =>  0.940778339045 , 'x' =>  0.832384893518 , 'invbeta' =>  0.782826528353 ),
                array( 'a' =>  0.367910930003 , 'b' =>  0.256497818824 , 'x' =>  0.709514297925 , 'invbeta' =>  0.959138395836 ),
            );

        foreach ($testCases as $test) {
            $a = $test['a'];
            $b = $test['b'];
            $x = $test['x'];
            $invbeta = $test['invbeta'];
            $this->assertEquals($invbeta, BetaFunction::inverseIncompleteBetaFunction($x, $a, $b), 'Expected inomplete beta function value II($x, $a, $b) not matching', $accuracy);
        }
    }

    public function testContinuedFraction()
    {
    }
}
