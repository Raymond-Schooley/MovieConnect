#Open up the tsv file Principal created with mysql and use this info to create an edge list

def main():
    tab = "\t"
    comma = ","
    white_space = " "
    principal = open("PRINCIPAL.tsv", "r")
    edge_list = open("EdgeList.tsv", "w")

    for line in principal:
        
        find_tab = line.find(tab, 0, len(line))
        #the current position in this line
        beg = find_tab + 1
        end = find_tab + 1

        movie = line[0: find_tab].strip()
        while line.find(comma, beg, len(line)) != -1:
            end = line.find(comma, beg, len(line))
            person = line[beg: end].strip()
            beg = end + 1
            edge_list.write(movie + tab + person + "\n")
        #grab the last name of the line
        person = line[beg: len(line)].strip()
        edge_list.write(movie + tab + person + "\n")

    principal.close()
    edge_list.close()

main()
