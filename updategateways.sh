#!/bin/bash
# Scans for OmniPay gateways that are missing from the common/composer.json GateWay listing
# and optionally inserts any missing ones
errors=0; 
base="$(readlink -f "$(dirname "$0")")";
cd "$base/../../"
prefix="$PWD";
for name in $(for file in $(grep -l "^class .* extends " */*/src/*Gateway.php); do 
    rest="$(grep '^class .*Gateway extends' $file | cut -d" " -f2 | sed s#"Gateway$"#""#g)";  
    name="$(grep '^namespace' $file | head -n 1 | cut -d\\ -f2 | cut -d\; -f1)" ; 
    if [ "$rest" != "" ]; then
        name="${name}_${rest}"; 
    fi; 
    echo $name; 
done | sort ); do  
f="$(grep "$name" $base/composer.json)"; 
if [ "$f" = "" ]; then 
    echo "$name not present in $(echo "$base" | sed s#"$prefix/"#""#g)/composer.json" ;
    errors=$(($errors + 1)); 
    if [ "$FIX" = "1" ]; then 
        sed s#"^\( *\)\(\"gateways\": \[\)"#"\1\2\n\1    \"$name\","#g -i $base/composer.json; 
    fi; 
fi;  
done; 
if [ $errors -gt 0 ]; then 
    if [ "$FIX" != "1" ]; then
        echo -e "$errors Errors\nset environment variable FIX=1 and call again to add the missing gateways"; 
    else 
        echo "Added $errors new Gateways";
    fi;
fi
